<?php

namespace Mindee\Image;

use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Parsing\DependencyChecker;
use Mindee\Error\ErrorCode;

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
     *  //phpcs:disable
     * @throws MindeeUnhandledException Throws if one of the dependencies isn't installed.
     */
    public static function compress(
        $inputImage,
        ?int $quality = 85,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): \CURLFile {
        //phpcs: enable
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        try {
            $image = ImageUtils::toMagickImage($inputImage);
            $initialImage = $image->clone();
            $initialFileSize = $image->getImageLength();
            ImageUtils::resizeImage($image, $maxWidth, $maxHeight);
            ImageUtils::compressImageQuality($image, $quality);

            $finalImageSize = $image->getImageLength();
            if ($initialFileSize < $finalImageSize) {
                error_log("\033[33m[WARNING] Output image would be larger than input. Aborting operation.\033[0m\n");
                return ImageUtils::toCURLFile($initialImage);
            }
            return ImageUtils::toCURLFile($image);
        } catch (\Exception $e) {
            throw new MindeeImageException("Image compression failed.", ErrorCode::FILE_OPERATION_ABORTED, $e);
        }
    }
}
