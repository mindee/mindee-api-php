<?php

namespace Mindee\Image;

use Mindee\Error\MindeeImageException;

/**
 * Image compressor class to handle image compression.
 */
class ImageCompressor
{
    /**
     * @param mixed        $inputImage Input image. Accepts SplFileObject, CURLFile, Imagick & resources.
     * @param integer|null $quality    Quality to apply to the image.
     * @param integer|null $maxWidth   Maximum width to constrain the image to.
     *                Defaults to the image's size if unset.
     * @param integer|null $maxHeight  Maximum Height to constrain the image to.
     *              Defaults to the image's size if unset.
     * @return \CURLFile Curlfile handle for the image.
     * @throws MindeeImageException Throws if image processing fails.
     */
    public static function compressImage(
        $inputImage,
        ?int $quality = 85,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): \CURLFile {
        try {
            $image = ImageUtils::toMagickImage($inputImage);
            ImageUtils::resizeImage($image, $maxWidth, $maxHeight);
            ImageUtils::compressImageQuality($image, $quality);
            return ImageUtils::toCURLFile($image);
        } catch (\Exception $e) {
            throw new MindeeImageException("Image compression failed.", Mindee\Error\ErrorCode::OPERATION_ABORTED, $e);
        }
    }
}
