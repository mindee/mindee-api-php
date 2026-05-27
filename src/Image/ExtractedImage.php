<?php

declare(strict_types=1);

namespace Mindee\Image;

use Imagick;
use ImagickException;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Input\BytesInput;

use function in_array;

use const DIRECTORY_SEPARATOR;

/**
 * An extracted sub-image.
 */
class ExtractedImage
{
    /**
     * Initializes a new instance of the ExtractedImage class.
     *
     * @param Imagick $image The extracted image.
     * @param string $filename The filename for the image.
     * @param string $saveFormat The format to save the image.
     * @param integer $pageId The page index of the image.
     * @param integer $elementId The element index of the image.
     *
     * @throws MindeeUnhandledException Throws if PDF operations aren't supported.
     */
    public function __construct(public Imagick $image, public string $filename, protected string $saveFormat, public int $pageId, public int $elementId)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
    }

    /**
     * Writes the image to a file.
     * Uses the default image format and filename.
     *
     * @param string $outputPath The output directory (must exist).
     * @param null|string $format The image format to use. Defaults to the save format if not provided.
     * @param integer $quality Quality of the saved image.
     *
     * @throws ImagickException Throws if the image can't be processed.
     */
    public function writeToFile(string $outputPath, ?string $format = null, int $quality = 100): void
    {
        $imagePath = $outputPath . DIRECTORY_SEPARATOR . $this->filename;
        $format = $this->getEncodedImageFormat($format ?? $this->saveFormat);
        $this->image->setImageFormat($format);
        $this->image->stripImage();
        $quality = min(100, max(0, $quality));
        if ('png' === $format) {
            $finalQuality = round($quality * 0.09);
            $this->image->setOption('png:compression-level', (string) $finalQuality);
        } elseif (in_array($format, ['jpg', 'jpeg'], true)) {
            $this->image->setImageCompression(Imagick::COMPRESSION_JPEG);
        }
        $this->image->setImageCompressionQuality($quality);
        $this->image->writeImage($imagePath);
    }

    /**
     * Returns the image in a format suitable for sending to a client for parsing.
     *
     * @return BytesInput Bytes input for the image.
     *
     * @throws ImagickException Throws if the image can't be processed.
     */
    public function asInputSource(): BytesInput
    {
        $format = $this->getEncodedImageFormat($this->saveFormat);
        $this->image->setImageFormat($format);

        return new BytesInput($this->image->getImageBlob(), $this->filename);
    }

    /**
     * Get the encoded image format.
     *
     * @param string $saveFormat Format to save the file as.
     * @return string Encoded image format.
     */
    private function getEncodedImageFormat(string $saveFormat): string
    {
        return match (strtolower($saveFormat)) {
            'png' => 'png',
            'bmp', => 'bmp',
            'gif' => 'gif',
            'webp' => 'webp',
            default => 'jpeg',
        };
    }
}
