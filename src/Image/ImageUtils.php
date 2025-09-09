<?php

namespace Mindee\Image;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;

/**
 * Miscellaneous image operations.
 */
class ImageUtils
{
    /**
     * @param mixed $image Image handle.
     * @return \Imagick A valid Imagick handle, CURLFile, SplFileObject or resource.
     * The resulting image is formatted to jpeg.
     * @throws MindeeImageException Throws if something goes wrong during image conversion.
     */
    public static function toMagickImage(mixed $image): \Imagick
    {
        try {
            if ($image instanceof \Imagick) {
                $imagickHandle = $image;
                $imagickHandle->setImageFormat('jpeg');
            } elseif ($image instanceof \SplFileObject) {
                $imagickHandle = new \Imagick();
                $imagickHandle->readImage($image->getRealPath());
            } elseif ($image instanceof \CURLFile) {
                $imagickHandle = new \Imagick();
                $imagickHandle->readImage($image->getFilename());
            } elseif (is_string($image) && file_exists($image) && is_file($image)) {
                $imagickHandle = new \Imagick();
                $imagickHandle->readImage($image);
            } elseif (is_resource($image)) {
                $imagickHandle = new \Imagick();
                $imagickHandle->readImageBlob($image);
            } else {
                throw new MindeeImageException(
                    'Input image must be a SplFileObject, path, resource or Imagick handle.'
                );
            }
            $imagickHandle->setImageFormat('jpeg');
            return $imagickHandle;
        } catch (MindeeImageException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new MindeeImageException(
                "Conversion to MagickImage failed.",
                ErrorCode::IMAGE_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * Resizes a provided MiniMagick Image with the given width & height, if present.
     *
     * @param \Imagick     $image  Imagick image handle.
     * @param integer|null $width  Width to comply with.
     * @param integer|null $height Height to comply with.
     * @return void
     * @throws \ImagickException Throws if resizing fails.
     */
    public static function resizeImage(\Imagick $image, ?int $width = null, int $height = null)
    {
        $width ??= $image->getImageWidth();
        $height ??= $image->getImageHeight();
        $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
    }


    /**
     * Compresses the quality of the provided MiniMagick image.
     * @param \Imagick $image   Imagick image handle.
     * @param integer  $quality Quality to apply to the image. This operation is independent of a JPG's base quality.
     * @return void
     * @throws \ImagickException Throws if compression fails.
     */
    public static function compressImageQuality(\Imagick $image, int $quality = 85)
    {
        $image->setImageCompressionQuality($quality);
    }

    /**
     * Converts an Imagick into a valid CURLFile handle.
     * @param \Imagick $image Imagick image handle.
     * @return \CURLFile
     * @throws MindeeImageException Throws if the image can't be converted back into a CURLFile.
     */
    public static function toCURLFile(\Imagick $image): \CURLFile
    {
        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'convert_image_');
            file_put_contents($tempFile, $image->getImageBlob());
            $filenameWithoutExtension = pathinfo($image->getFilename(), PATHINFO_FILENAME);
            return new \CURLFile($tempFile, 'image/jpeg', $filenameWithoutExtension . '.jpg');
        } catch (\Exception $e) {
            throw new MindeeImageException(
                "Conversion to CURLFile failed.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }
}
