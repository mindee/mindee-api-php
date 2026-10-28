<?php

declare(strict_types=1);

namespace Mindee\Image;

use BernardLedit\Image\Image;
use BernardLedit\Image\ImageException;
use BernardLedit\Image\ImageExtractException;
use BernardLedit\Pdf\PdfExtractException;
use BernardLedit\Pdf\PdfiumException;
use InvalidArgumentException;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePdfException;
use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Input\LocalInputSource;
use ValueError;

use function BernardLedit\Image\guessFormat;
use function BernardLedit\Image\extractFromPolygons;
use function count;
use function sprintf;
use function is_array;

/**
 * Extract sub-images from an image.
 */
class ImageExtractor
{
    /**
     * JPEG quality used for extracted crops, same as the Python SDK (sufficient for OCR).
     */
    private const CROP_QUALITY = 70;

    /**
     * Resolution PDF pages are rendered at before cropping, in dots per inch. Images are cropped at their own
     * resolution.
     */
    public const DEFAULT_PDF_DPI = 300.0;

    /**
     * @var Image[] Array of extracted page images.
     */
    protected array $pageImages = [];

    /**
     * @var string Name of the file.
     */
    protected string $filename;

    /**
     * @var string Format to save the image as.
     */
    protected string $saveFormat;

    /**
     * @var LocalInputSource Local input object used by the ImageExtractor.
     */
    protected LocalInputSource $inputSource;

    /**
     * @var string Contents of the input source, read once: some sources can only be read once.
     */
    protected string $sourceData;

    /**
     * @var integer Number of pages in the document.
     */
    public int $pageCount;

    /**
     * @param LocalInputSource $localInput Local input, accepts all compatible formats.
     * @param null|string $saveFormat Save format, will be coerced to jpg by default.
     * @param float $pdfDpi Resolution PDF pages are rendered at before cropping; ignored for images.
     *
     * @throws MindeePdfException Throws if PDF operations aren't supported, or if the file can't be read, respectively.
     */
    public function __construct(
        LocalInputSource $localInput,
        ?string          $saveFormat = null,
        protected float  $pdfDpi = self::DEFAULT_PDF_DPI
    ) {
        DependencyChecker::requireBernardLedit();
        $this->filename = $localInput->fileName;
        $this->inputSource = $localInput;
        $this->sourceData = $localInput->readContents()[1];

        $extension = pathinfo($localInput->fileName, PATHINFO_EXTENSION);
        if (null === $saveFormat) {
            if ($extension && 'pdf' !== strtolower($extension)) {
                $this->saveFormat = $extension;
            } else {
                $this->saveFormat = 'jpg';
            }
        } else {
            $this->saveFormat = $saveFormat;
        }

        if ($localInput->isPdf()) {
            $this->pageCount = $localInput->pageCount ?? 1;
        } else {
            try {
                guessFormat($this->sourceData);
            } catch (ImageException $e) {
                throw new MindeeImageException(
                    "Image couldn't be processed.",
                    ErrorCode::IMAGE_CANT_PROCESS,
                    $e
                );
            }
            $this->pageCount = 1;
        }
    }


    /**
     * Extracts images from a page.
     *
     * @param array<Polygon|array<Point>> $polygons List of polygons to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @param null|string $filenamePrefix Output filename prefix.
     * @param null|string $format Save format for extracted images. Defaults to the original format.
     * @param null|array<int> $indexes Index of each polygon, used in its image's file name and index, in the
     *                                 same order as the polygons. Defaults to 0, 1, 2...
     *
     * @return array<ExtractedImage> An array of created images
     * @throws MindeeImageException Throws if the image can't be processed.
     */
    public function extractMultipleImagesFromSource(
        array   $polygons,
        int     $pageIndex,
        ?string $filenamePrefix = null,
        ?string $format = null,
        ?array  $indexes = null
    ): array {
        $saveFormat = $format ?? $this->saveFormat;
        $filenamePrefix ??= $this->filename;
        $polygons = array_values($polygons);
        $indexes = null === $indexes ? array_keys($polygons) : array_values($indexes);
        if (count($indexes) !== count($polygons)) {
            throw new InvalidArgumentException(
                sprintf('Got %d indexes for %d polygons.', count($indexes), count($polygons))
            );
        }
        $crops = $this->cropPolygons($polygons, $pageIndex);

        $extractedImages = [];
        foreach ($crops as $i => $crop) {
            $index = $indexes[$i];
            $outputFilename = sprintf('%s-%d.%s', $filenamePrefix, $index, $saveFormat);
            $extractedImages[] = $this->encodeCrop($crop, $pageIndex, $index, $outputFilename, $saveFormat);
        }

        return $extractedImages;
    }

    /**
     * Extracts a cropped portion from an image.
     *
     * @param Polygon|array<Point> $polygon Polygon to extract.
     * @param integer $pageIndex Page index to extract from.
     * @param integer $index Index to use for naming the extracted image.
     * @param null|string $filename Output filename.
     * @param null|string $format Output format.
     *
     * @return ExtractedImage Extracted image data.
     * @throws MindeeImageException Throws if the image can't be processed.
     */
    public function extractPolygonFromPage(
        Polygon|array $polygon,
        int           $pageIndex,
        int           $index,
        ?string       $filename = null,
        ?string       $format = null
    ): ExtractedImage {
        $format ??= $this->saveFormat;
        [$crop] = $this->cropPolygons([$polygon], $pageIndex);
        $filename ??= sprintf('%s_page%d-%d.%s', $this->filename, $pageIndex, $index, $format);

        return $this->encodeCrop($crop, $pageIndex, $index, $filename, $format);
    }


    /**
     * Getter for the local input source.
     */
    public function getInputSource(): LocalInputSource
    {
        return $this->inputSource;
    }

    /**
     * Crops every polygon's bounding box from a page, in a single native call.
     *
     * @param array<Polygon|array<Point>> $polygons Polygons to crop.
     * @param integer $pageIndex Page index, begins at 0.
     * @return Image[] One image per polygon, in the same order.
     * @throws MindeeImageException Throws if the page or polygons can't be processed.
     */
    protected function cropPolygons(array $polygons, int $pageIndex): array
    {
        $pairs = array_map(static::polygonToPairs(...), $polygons);
        try {
            return extractFromPolygons($this->sourceData, $pageIndex, $pairs, $this->pdfDpi);
        } catch (ImageException|ImageExtractException|PdfExtractException|PdfiumException|ValueError $e) {
            throw new MindeeImageException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Encodes a cropped image and wraps it.
     *
     * @throws MindeeImageException Throws if encoding fails.
     */
    protected function encodeCrop(
        Image  $crop,
        int    $pageIndex,
        int    $index,
        string $filename,
        string $format
    ): ExtractedImage {
        try {
            $data = $crop->encode(ExtractedImage::getEncodedImageFormat($format), self::CROP_QUALITY);
        } catch (ImageException|ValueError $e) {
            throw new MindeeImageException($e->getMessage(), $e->getCode(), $e);
        }

        return new ExtractedImage($data, $filename, $format, $pageIndex, $index);
    }

    /**
     * Converts a polygon to the `[[x, y], ...]` shape the native extension expects.
     *
     * @param Polygon|array<Point> $polygon Polygon or list of points.
     * @return array<array{0: float, 1: float}>
     */
    protected static function polygonToPairs(Polygon|array $polygon): array
    {
        $points = is_array($polygon) ? $polygon : $polygon->getCoordinates();

        return array_map(
            static fn(Point $p): array => [$p->getX(), $p->getY()],
            array_values($points)
        );
    }
}
