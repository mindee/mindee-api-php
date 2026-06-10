<?php

declare(strict_types=1);

namespace Mindee\Image;

use Exception;
use Imagick;
use ImagickException;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePdfException;
use Mindee\Geometry\BBox;
use Mindee\Geometry\BBoxUtils;
use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Input\LocalInputSource;

use function count;
use function sprintf;

/**
 * Extract sub-images from an image.
 */
class ImageExtractor
{
    /**
     * @var Imagick[] Array of extracted page images.
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
     * @var integer Number of pages in the document.
     */
    public int $pageCount;

    /**
     * @param LocalInputSource $localInput Local input, accepts all compatible formats.
     * @param null|string $saveFormat Save format, will be coerced to jpg by default.
     *
     * @throws MindeePdfException Throws if PDF operations aren't supported, or if the file can't be read, respectively.
     */
    public function __construct(LocalInputSource $localInput, ?string $saveFormat = null)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        $this->filename = $localInput->fileName;
        $this->inputSource = $localInput;

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

        if ($this->inputSource->isPdf()) {
            $this->pageImages = static::pdfToImages($this->inputSource->readContents()[1]);
        } else {
            try {
                $image = new Imagick();
                $image->readImageBlob($this->inputSource->readContents()[1]);
            } catch (ImagickException $e) {
                throw new MindeePdfException(
                    "Image couldn't be processed.",
                    ErrorCode::IMAGE_CANT_PROCESS,
                    $e
                );
            }
            $this->pageImages[] = $image;
        }
        $this->pageCount = count($this->pageImages);
    }

    /**
     * Renders the input PDF's pages as individual images.
     *
     * @param string $fileBytes Input pdf.
     *
     * @return Imagick[] A list of pages.
     *
     * @throws MindeeImageException Throws if the image can't be handled.
     */
    public static function pdfToImages(string $fileBytes): array
    {
        try {
            $images = [];
            $imagick = new Imagick();
            $imagick->readImageBlob($fileBytes);

            foreach ($imagick as $page) {
                $page->setImageFormat('jpg');
                $images[] = $page;
            }

            return $images;
        } catch (ImagickException $e) {
            throw new MindeeImageException(
                "Couldn't convert PDF to images.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }


    /**
     * Extracts images from a page.
     *
     * @param array<Polygon|array<Point>> $polygons List of polygons to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @param null|string $filenamePrefix Output filename prefix.
     * @param null|string $format Save format for extracted images. Defaults to the original format.
     *
     * @return array<ExtractedImage> An array of created images
     * @throws MindeeImageException Throws if the image can't be processed.
     */
    public function extractPolygonsFromPage(
        array $polygons,
        int $pageIndex,
        ?string $filenamePrefix = null,
        ?string $format = null
    ): array {
        $saveFormat = $format ?? $this->saveFormat;
        $extractedImages = [];

        try {
            foreach ($polygons as $i => $polygon) {
                $filenamePrefix ??= $this->filename;
                $outputFilename = sprintf('%s-%d.%s', $filenamePrefix, $i, $saveFormat);
                $extractedImages[] = $this->extractPolygonFromPage(
                    $polygon,
                    $pageIndex,
                    $i,
                    $outputFilename,
                    $saveFormat
                );
            }
        } catch (Exception $e) {
            throw new MindeeImageException($e->getMessage(), $e->getCode(), $e);
        }

        return $extractedImages;
    }

    /**
     * Extracts a cropped portion from an image.
     *
     * @param Polygon $polygon Polygon to extract.
     * @param integer $pageIndex Page index to extract from.
     * @param integer $index Index to use for naming the extracted image.
     * @param null|string $filename Output filename.
     * @param null|string $format Output format.
     *
     * @return ExtractedImage Extracted image data.
     * @throws MindeeImageException Throws if the image can't be processed.
     */
    public function extractPolygonFromPage(
        Polygon $polygon,
        int $pageIndex,
        int $index,
        ?string $filename = null,
        ?string $format = null
    ): ExtractedImage {
        $bbox = BBoxUtils::generateBBoxFromPolygon($polygon);
        try {
            $extractedImageData = $this->extractImageFromBbox($bbox, $pageIndex);
        } catch (ImagickException $e) {
            throw new MindeeImageException($e->getMessage(), $e->getCode(), $e);
        }
        $format ??= $this->saveFormat;
        $filename ??= sprintf('%s_page%d-%d.%s', $this->filename, $pageIndex, $index, $format);
        return new ExtractedImage($extractedImageData, $filename, $format, $pageIndex, $index);
    }


    /**
     * Getter for the local input source.
     */
    public function getInputSource(): LocalInputSource
    {
        return $this->inputSource;
    }

    /**
     * Extracts an image from a set of coordinates.
     *
     * @param BBox $bbox BBox coordinates.
     * @param integer|float $pageIndex The page index to extract, begins at 0.
     * @throws ImagickException Throws if the image can't be processed.
     */
    protected function extractImageFromBbox(BBox $bbox, int|float $pageIndex): Imagick
    {
        $image = $this->pageImages[$pageIndex]->clone();
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        $minX = round($bbox->getMinX() * $width);
        $maxX = round($bbox->getMaxX() * $width);
        $minY = round($bbox->getMinY() * $height);
        $maxY = round($bbox->getMaxY() * $height);

        $image->cropImage((int) ($maxX - $minX), (int) ($maxY - $minY), (int) $minX, (int) $minY);

        return $image;
    }

    /**
     * Splits the filename into name and extension.
     *
     * @param string $filename Name of the file.
     * @return array{0: string, 1: string} An array containing the name and extension of the file.
     */
    protected static function splitNameStrict(string $filename): array
    {
        return [
            pathinfo($filename, PATHINFO_FILENAME),
            pathinfo($filename, PATHINFO_EXTENSION),
        ];
    }
}
