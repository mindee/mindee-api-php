<?php

declare(strict_types=1);

namespace Mindee\Image;

use BernardLedit\Image\ImageException;
use Exception;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeeUnhandledException;
use ValueError;

use function BernardLedit\Image\compress as compressImage;

/**
 * Image compressor class to handle image compression.
 */
class ImageCompressor
{
    /**
     * @param string $inputImage Raw bytes of the input image.
     * @param integer $quality Quality to apply to the image (JPEG compression).
     * @param integer|null $maxWidth Maximum width to constrain the image to.
     *                               Defaults to the image's size if unset.
     * @param integer|null $maxHeight Maximum Height to constrain the image to.
     *                                Defaults to the image's size if unset.
     * @return string Raw bytes of the compressed JPEG image.
     * @throws MindeeImageException Throws if image processing fails.
     * @throws MindeeUnhandledException Throws if the bernard_ledit extension isn't loaded.
     */
    public static function compress(
        string $inputImage,
        int $quality = 85,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): string {
        DependencyChecker::requireBernardLedit();
        try {
            [$bytes] = compressImage($inputImage, $quality, $maxWidth, $maxHeight);
            return $bytes;
        } catch (Exception $e) {
            throw new MindeeImageException("Image compression failed.", ErrorCode::FILE_OPERATION_ABORTED, $e);
        }
    }
}
