<?php

declare(strict_types=1);

namespace Mindee\V1\Image;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeGeometryException;
use Mindee\Geometry\Polygon;
use Mindee\Image\ExtractedImage;
use Mindee\Image\ImageExtractor as BaseImageExtractor;
use Mindee\V1\Parsing\Standard\BaseField;

use function sprintf;

/**
 * Wrapper class for V1 of the BaseImageExtractor.
 */
class ImageExtractor extends BaseImageExtractor
{
    /**
     * Extract multiple images on a given page from a list of fields having position data.
     *
     * @param array<BaseField<string|float|integer|boolean|Polygon>> $fields List of Fields to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @param null|string $outputName The base output filename, must have an image extension.
     *
     * @return array<ExtractedImage> a list of extracted images
     */
    public function extractImagesFromPage(array $fields, int $pageIndex, ?string $outputName = null): array
    {
        $outputName ??= $this->filename;
        return $this->extractFromPage($fields, $pageIndex, $outputName);
    }

    /**
     * Extracts a single image from a Position field.
     *
     * @param BaseField<string|float|integer|boolean|Polygon> $field The field to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @param integer $index The index to use for naming the extracted image.
     * @param string $filename The output filename.
     * @param string $format The output format.
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
     * Extracts images from a page.
     *
     * @param array<BaseField<string|float|integer|boolean|Polygon>> $fields List of Fields to extract.
     * @param integer $pageIndex The page index to extract, begins at 0.
     * @param string $outputName Name of the created file.
     * @param string $format The output format.
     *
     * @return array<ExtractedImage> An array of created images
     */
    protected function extractFromPage(array $fields, int $pageIndex, string $outputName, string $format = 'jpg'): array
    {
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

}
