<?php

namespace Mindee\Extraction;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeGeometryException;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePDFException;
use Mindee\Geometry\BBox;
use Mindee\Geometry\BBoxUtils;
use Mindee\Geometry\Polygon;
use Mindee\Input\LocalInputSource;
use Mindee\Parsing\DependencyChecker;
use Mindee\Parsing\Standard\BaseField;

/**
 * Extract sub-images from an image.
 */
class ImageExtractor
{
    /**
     * @var \Imagick[] Array of extracted page images.
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
     * @param LocalInputSource $localInput Local input, accepts all compatible formats.
     * @param null|string      $saveFormat Save format, will be coerced to jpg by default.
     *
     * @throws MindeePDFException Throws if PDF operations aren't supported, or if the file can't be read, respectively.
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

        if ($this->inputSource->isPDF()) {
            $this->pageImages = $this->pdfToImages($this->inputSource->readContents()[1]);
        } else {
            try {
                $image = new \Imagick();
                $image->readImageBlob($this->inputSource->readContents()[1]);
            } catch (\ImagickException $e) {
                throw new MindeePDFException(
                    "Image couldn't be processed.",
                    ErrorCode::IMAGE_CANT_PROCESS,
                    $e
                );
            }
            $this->pageImages[] = $image;
        }
    }

    /**
     * Renders the input PDF's pages as individual images.
     *
     * @param string $fileBytes Input pdf.
     *
     * @return \Imagick[] A list of pages.
     *
     * @throws MindeeImageException Throws if the image can't be handled.
     */
    public static function pdfToImages(string $fileBytes): array
    {
        try {
            $images = [];
            $imagick = new \Imagick();
            $imagick->readImageBlob($fileBytes);

            foreach ($imagick as $page) {
                $page->setImageFormat('jpg');
                $images[] = $page;
            }

            return $images;
        } catch (\ImagickException $e) {
            throw new MindeeImageException(
                "Couldn't convert PDF to images.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }

    /**
     * Gets the number of pages in the file.
     * @return integer Page count.
     */
    public function getPageCount(): int
    {
        return count($this->pageImages);
    }

    /**
     * Extract multiple images on a given page from a list of fields having position data.
     *
     * @param array       $fields     List of Fields to extract.
     * @param integer     $pageIndex  The page index to extract, begins at 0.
     * @param null|string $outputName The base output filename, must have an image extension.
     *
     * @return array a list of extracted images
     */
    public function extractImagesFromPage(array $fields, int $pageIndex, ?string $outputName = null): array
    {
        $outputName ??= $this->filename;
        return $this->extractFromPage($fields, $pageIndex, $outputName);
    }

    /**
     * Extracts images from a page.
     *
     * @param array       $polygons  List of polygons to extract.
     * @param integer     $pageIndex The page index to extract, begins at 0.
     * @param null|string $format    Save format for extracted images. Defaults to the original format.
     *
     * @return array an array of created images
     */
    public function extractPolygonsFromPage(array $polygons, int $pageIndex, ?string $format = null): array
    {
        $saveFormat = $format ?? $this->saveFormat;
        $extractedImages = [];

        foreach ($polygons as $i => $polygon) {
            $extractedImages[] = $this->extractPolygonFromPage($polygon, $pageIndex, $i, null, $format);
        }

        return $extractedImages;
    }

    /**
     * Extracts a cropped portion from an image.
     *
     * @param Polygon     $polygon   Polygon to extract.
     * @param integer     $pageIndex Page index to extract from.
     * @param integer     $index     Index to use for naming the extracted image.
     * @param null|string $filename  Output filename.
     * @param null|string $format    Output format.
     *
     * @return ExtractedImage Extracted image data.
     * @throws \ImagickException Throws if the image can't be processed.
     */
    public function extractPolygonFromPage(
        Polygon $polygon,
        int $pageIndex,
        int $index,
        ?string $filename = null,
        ?string $format = null
    ): ExtractedImage {
        $bbox = BBoxUtils::generateBBoxFromPolygon($polygon);
        $extractedImageData = $this->extractImageFromBbox($bbox, $pageIndex);
        $filename ??= $this->filename;
        $format ??= $this->saveFormat;
        $filename ??= sprintf('%s.%s_page%d-%d.%s', $filename, $format, $pageIndex, $index, $format);

        return new ExtractedImage($extractedImageData, $filename, $format, $pageIndex, $index);
    }

    /**
     * Extracts a single image from a Position field.
     *
     * @param BaseField $field     The field to extract.
     * @param integer   $pageIndex The page index to extract, begins at 0.
     * @param integer   $index     The index to use for naming the extracted image.
     * @param string    $filename  The output filename.
     * @param string    $format    The output format.
     *
     * @return null|ExtractedImage The extracted image, or null if the field does not have valid position data.
     *
     * @throws MindeeGeometryException Throws if a field does not contain positional data.
     */
    public function extractImage(
        BaseField $field,
        int $pageIndex,
        int $index,
        string $filename,
        string $format
    ): ?ExtractedImage {
        $polygon = null;

        if (!empty($field->polygon)) {
            $polygon = $field->polygon;
        } elseif (!empty($field->boundingBox)) {
            $polygon = $field->boundingBox;
        } elseif (!empty($field->quadrangle)) {
            $polygon = $field->quadrangle;
        } elseif (!empty($field->rectangle)) {
            $polygon = $field->rectangle;
        }

        if (null === $polygon) {
            throw new MindeeGeometryException(
                'Provided field has no valid position data.',
                ErrorCode::GEOMETRIC_OPERATION_FAILED
            );
        }

        return $this->extractPolygonFromPage($polygon, $pageIndex, $index, $filename, $format);
    }

    /**
     * Getter for the local input source.
     * @return LocalInputSource
     */
    public function getInputSource(): LocalInputSource
    {
        return $this->inputSource;
    }

    /**
     * Extracts images from a page.
     *
     * @param array   $fields     List of Fields to extract.
     * @param integer $pageIndex  The page index to extract, begins at 0.
     * @param string  $outputName Name of the created file.
     * @param string  $format     The output format.
     *
     * @return array an array of created images
     */
    protected function extractFromPage(array $fields, int $pageIndex, string $outputName, string $format = 'jpg'): array
    {
        $format ??= $this->saveFormat;
        $extractedImages = [];

        $i = 0;
        foreach ($fields as $field) {
            $filename = sprintf('%s_page%d-%d.%s', $outputName, $pageIndex, $i, $format);
            $extractedImage = $this->extractImage($field, $pageIndex, $i, $filename, $format);
            if (null !== $extractedImage) {
                $extractedImages[] = $extractedImage;
            }
            ++$i;
        }

        return $extractedImages;
    }

    /**
     * Extracts an image from a set of coordinates.
     *
     * @param BBox          $bbox      BBox coordinates.
     * @param integer|float $pageIndex The page index to extract, begins at 0.
     * @return \Imagick
     * @throws \ImagickException Throws if the image can't be processed.
     */
    protected function extractImageFromBbox(BBox $bbox, int|float $pageIndex): \Imagick
    {
        $image = $this->pageImages[$pageIndex]->clone();
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        $minX = round($bbox->getMinX() * $width);
        $maxX = round($bbox->getMaxX() * $width);
        $minY = round($bbox->getMinY() * $height);
        $maxY = round($bbox->getMaxY() * $height);

        $image->cropImage((int)($maxX - $minX), (int)($maxY - $minY), (int)$minX, (int)$minY);

        return $image;
    }

    /**
     * Splits the filename into name and extension.
     *
     * @param string $filename Name of the file.
     * @return array An array containing the name and extension of the file.
     */
    protected static function splitNameStrict(string $filename): array
    {
        return [
            pathinfo($filename, PATHINFO_FILENAME),
            pathinfo($filename, PATHINFO_EXTENSION),
        ];
    }
}
