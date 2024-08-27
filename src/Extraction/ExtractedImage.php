<?php

namespace Mindee\Extraction;

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
    private $image;

    /**
     * Name of the file.
     *
     * @var string
     */
    protected string $filename;

    /**
     * String representation of the save format.
     *
     * @var string
     */
    protected string $saveFormat;

    /**
     * Initializes a new instance of the ExtractedImage class.
     *
     * @param mixed  $image      The extracted image. Not explicitly typed as \ImageMagick to avoid errors.
     * @param string $filename   The filename for the image.
     * @param string $saveFormat The format to save the image.
     */
    public function __construct($image, string $filename, string $saveFormat)
    {
        $this->image = $image;
        $this->filename = $filename;
        $this->saveFormat = $saveFormat;
    }

    /**
     * Writes the image to a file.
     * Uses the default image format and filename.
     *
     * @param string $outputPath The output directory (must exist).
     * @throws \ImagickException Throws if the image can't be processed.
     * @return void
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
     * @return string Binary string of the image data
     */
    public function asInputSource(): string
    {
        $format = $this->getEncodedImageFormat($this->saveFormat);
        $this->image->setImageFormat($format);
        return $this->image->getImageBlob();
    }

    /**
     * Get the encoded image format.
     *
     * @param string $saveFormat Format to save the file as.
     * @return string
     */
    private function getEncodedImageFormat(string $saveFormat): string
    {
        switch (strtolower($saveFormat)) {
            case 'png':
                return 'png';
            case 'bmp':
                return 'bmp';
            case 'gif':
                return 'gif';
            case 'webp':
                return 'webp';
            default:
                return 'jpeg';
        }
    }
}
