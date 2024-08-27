<?php

namespace Mindee\Extraction;

use Mindee\Error\MindeeGeometryException;
use Mindee\Geometry\BBox;
use Mindee\Geometry\BBoxUtils;
use Mindee\Input\LocalInputSource;

/**
 * Extract sub-images from an image.
 */
class ImageExtractor
{
    /**
     * Array of extracted page images.
     *
     * @var array
     */
    private array $pageImages = [];
    /**
     * Name of the file.
     *
     * @var string
     */
    private string $filename;
    /**
     * Format to save the image as.
     *
     * @var string
     */
    private string $saveFormat;
    /**
     * Local input object used by the ImageExtractor.
     *
     * @var LocalInputSource
     */
    protected LocalInputSource $inputSource;


    /**
     * @param LocalInputSource $localInput Local Input, accepts all compatible formats.
     * @param string|null      $saveFormat Save format, will be coerced to jpg by default.
     * @throws \ImagickException Throws if ImageMagick can't handle the image.
     */
    public function __construct(LocalInputSource $localInput, ?string $saveFormat = null)
    {
        $this->filename = $localInput['filename'];
        $this->inputSource = $localInput;

        if ($saveFormat === null) {
            $extension = pathinfo($localInput['filename'], PATHINFO_EXTENSION);
            if ($extension && strtolower($extension) !== 'pdf') {
                $this->saveFormat = $extension;
            } else {
                $this->saveFormat = 'jpg';
            }
        } else {
            $this->saveFormat = $saveFormat;
        }

        if ($this->inputSource->isPDF()) {
            $this->pageImages = $this->pdfToImages($localInput['bytes']);
        } else {
            $image = new \Imagick();
            $image->readImageBlob($localInput['bytes']);
            $this->pageImages[] = $image;
        }
    }

    /**
     * Renders the input PDF's pages as individual images.
     *
     * @param string $fileBytes Input pdf.
     * @return array A list of pages.
     */
    public static function pdfToImages(string $fileBytes): array
    {
        $images = [];
        $imagick = new \Imagick();
        $imagick->readImageBlob($fileBytes);

        foreach ($imagick as $page) {
            $page->setImageFormat('png');
            $images[] = $page;
        }

        return $images;
    }

    /**
     * Gets the number of pages in the file.
     *
     * @return integer
     */
    public function getPageCount(): int
    {
        return count($this->pageImages);
    }

    /**
     * Extract multiple images on a given page from a list of fields having position data.
     *
     * @param array   $fields    List of Fields to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @return array A list of extracted images.
     */
    public function extractImagesFromPage(array $fields, int $pageIndex): array
    {
        return $this->extractFromPage($fields, $pageIndex, $this->filename);
    }

    /**
     * Extracts images from a page.
     *
     * @param array   $fields     List of Fields to extract.
     * @param integer $pageIndex  The page index to extract, begins at 0.
     * @param string  $outputName Name of the created file.
     * @return array An array of created images.
     */
    private function extractFromPage(array $fields, int $pageIndex, string $outputName): array
    {
        $splitName = $this->splitNameStrict($outputName);
        $filename = sprintf("%s_page-%03d.%s", $splitName[0], $pageIndex + 1, $this->saveFormat);
        $extractedImages = [];

        foreach ($fields as $i => $field) {
            $extractedImage = $this->extractImage($field, $pageIndex, $i + 1, $filename);
            if ($extractedImage !== null) {
                $extractedImages[] = $extractedImage;
            }
        }

        return $extractedImages;
    }

    /**
     * Extracts a single image from a Position field.
     *
     * @param array   $field     The field to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @param integer $index     The index to use for naming the extracted image.
     * @param string  $filename  The output filename.
     * @return ExtractedImage|null The extracted image, or null if the field does not have valid position data.
     * @throws MindeeGeometryException Throws if a field does not contain positional data.
     */
    public function extractImage(array $field, int $pageIndex, int $index, string $filename): ?ExtractedImage
    {
        $splitName = $this->splitNameStrict($filename);
        $boundingBox = null;

        if (!empty($field['polygon'])) {
            $boundingBox = $field['polygon'];
        } elseif (!empty($field['bounding_box'])) {
            $boundingBox = $field['bounding_box'];
        } elseif (!empty($field['quadrangle'])) {
            $boundingBox = $field['quadrangle'];
        } elseif (!empty($field['rectangle'])) {
            $boundingBox = $field['rectangle'];
        }

        if ($boundingBox === null) {
            throw new MindeeGeometryException("Provided field has no valid position data.");
        }

        $bbox = BBoxUtils::generateBBoxFromPolygon($boundingBox);
        $fieldFilename = sprintf("%s_%03d.%s", $splitName[0], $index, $this->saveFormat);
        $extractedImageData = $this->extractImageFromBbox($bbox, $pageIndex);

        return new ExtractedImage($extractedImageData, $fieldFilename, $this->saveFormat);
    }

    /**
     * Getter for the local input source.
     *
     * @return LocalInputSource
     */
    public function getInputSource(): LocalInputSource
    {
        return $this->inputSource;
    }

    /**
     * Extracts an image from a set of coordinates.
     *
     * @param Mindee\Geometry\BBox $bbox BBox coordinates.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @return \Imagick
     */
    private function extractImageFromBbox(Mindee\Geometry\BBox $bbox, int $pageIndex): Imagick
    {
        $image = $this->pageImages[$pageIndex];
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        $minX = round($bbox['min_x'] * $width);
        $maxX = round($bbox['max_x'] * $width);
        $minY = round($bbox['min_y'] * $height);
        $maxY = round($bbox['max_y'] * $height);

        $image->cropImage($maxX - $minX, $maxY - $minY, $minX, $minY);

        return $image;
    }

    /**
     * Splits the filename into name and extension.
     *
     * @param string $filename Name of the file.
     * @return array
     */
    private function splitNameStrict(string $filename): array
    {
        return [
            pathinfo($filename, PATHINFO_FILENAME),
            pathinfo($filename, PATHINFO_EXTENSION)
        ];
    }
}
