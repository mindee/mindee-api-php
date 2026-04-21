<?php

namespace Mindee\Extraction;

use Mindee\Error\MindeeUnhandledException;
use Mindee\Input\BytesInput;
use Mindee\Parsing\DependencyChecker;

/**
 * An extracted sub-image.
 */
class ExtractedImage
{
    /**
     * @var \Imagick Wrapper for the image.
     */
    public \Imagick $image;

    /**
     * @var string Name of the file.
     */
    public string $filename;

    /**
     * @var integer Page ID of the image.
     */
    public int $pageId;

    /**
     * @var integer Element ID of the image.
     */
    public int $elementId;

    /**
     * @var string String representation of the save format.
     */
    protected string $saveFormat;

    /**
     * Initializes a new instance of the ExtractedImage class.
     *
     * @param mixed   $image      The extracted image. Not explicitly typed as \Imagick to avoid errors.
     * @param string  $filename   The filename for the image.
     * @param string  $saveFormat The format to save the image.
     * @param integer $pageIndex  The page index of the image.
     * @param integer $index      The element index of the image.
     *
     * @throws MindeeUnhandledException Throws if PDF operations aren't supported.
     */
    public function __construct(mixed $image, string $filename, string $saveFormat, int $pageIndex, int $index)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        $this->image = $image;
        $this->filename = $filename;
        $this->saveFormat = $saveFormat;
        $this->pageId = $pageIndex;
        $this->elementId = $index;
    }

    /**
     * Writes the image to a file.
     * Uses the default image format and filename.
     *
     * @param string      $outputPath The output directory (must exist).
     * @param null|string $format     The image format to use. Defaults to the save format if not provided.
     * @param integer     $quality    Quality of the saved image.
     *
     * @return void
     * @throws \ImagickException Throws if the image can't be processed.
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
            $this->image->setOption('png:compression-level', $finalQuality);
        } elseif (in_array($format, ['jpg', 'jpeg'])) {
            $this->image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        }
        $this->image->setImageCompressionQuality($quality);
        $this->image->writeImage($imagePath);
    }

    /**
     * Returns the image in a format suitable for sending to a client for parsing.
     *
     * @return BytesInput Bytes input for the image.
     *
     * @throws \ImagickException Throws if the image can't be processed.
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
