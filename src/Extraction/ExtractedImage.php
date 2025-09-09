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
     * Imagick wrapper for the image.
     *
     * @var \Imagick
     */
    public \Imagick $image;

    /**
     * Name of the file.
     *
     * @var string
     */
    public string $filename;

    /**
     * String representation of the save format.
     *
     * @var string
     */
    protected string $saveFormat;

    /**
     * Initializes a new instance of the ExtractedImage class.
     *
     * @param mixed  $image      The extracted image. Not explicitly typed as \Imagick to avoid errors.
     * @param string $filename   The filename for the image.
     * @param string $saveFormat The format to save the image.
     * @throws MindeeUnhandledException Throws if PDF operations aren't supported.
     */
    public function __construct(mixed $image, string $filename, string $saveFormat)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        $this->image = $image;
        $this->filename = $filename;
        $this->saveFormat = $saveFormat;
    }

    /**
     * Writes the image to a file.
     * Uses the default image format and filename.
     *
     * @param string $outputPath The output directory (must exist).
     * @return void
     * @throws \ImagickException Throws if the image can't be processed.
     */
    public function writeToFile(string $outputPath): void
    {
        $imagePath = $outputPath . DIRECTORY_SEPARATOR . $this->filename;
        $format = $this->getEncodedImageFormat($this->saveFormat);
        $this->image->setImageFormat($format);
        $this->image->writeImage($imagePath);
    }

    /**
     * Returns the image in a format suitable for sending to a client for parsing.
     *
     * @throws \ImagickException Throws if the image can't be processed.
     * @return BytesInput Bytes input for the image.
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
     * @return string
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
