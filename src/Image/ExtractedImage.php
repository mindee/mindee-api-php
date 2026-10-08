<?php

declare(strict_types=1);

namespace Mindee\Image;

use BernardLedit\Image\ImageException;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Input\BytesInput;
use ValueError;

use function BernardLedit\Image\decode;

use const DIRECTORY_SEPARATOR;

/**
 * An extracted sub-image.
 */
class ExtractedImage
{
    /**
     * Initializes a new instance of the ExtractedImage class.
     *
     * @param string $buffer The extracted image, encoded in its save format.
     * @param string $filename The filename for the image.
     * @param string $saveFormat The format to save the image.
     * @param integer $pageId The page index of the image.
     * @param integer $elementId The element index of the image.
     *
     * @throws MindeeUnhandledException Throws if PDF operations aren't supported.
     */
    public function __construct(public string $buffer, public string $filename, protected string $saveFormat, public int $pageId, public int $elementId)
    {
        DependencyChecker::requireBernardLedit();
    }

    /**
     * Writes the image to a file.
     * Uses the default image format and filename.
     * The buffer is written as-is unless another format or a quality is requested, in which case it is re-encoded.
     *
     * @param string $outputPath The output directory (must exist).
     * @param null|string $format The image format to use. Defaults to the save format if not provided.
     * @param integer|null $quality Quality of the saved image (JPEG only). Re-encodes the image if set.
     *
     * @throws ImageException|ValueError Throws if the image can't be re-encoded.
     */
    public function writeToFile(string $outputPath, ?string $format = null, ?int $quality = null): void
    {
        $imagePath = $outputPath . DIRECTORY_SEPARATOR . $this->filename;
        $encodedFormat = self::getEncodedImageFormat($format ?? $this->saveFormat);
        if (null === $quality && $encodedFormat === self::getEncodedImageFormat($this->saveFormat)) {
            file_put_contents($imagePath, $this->buffer);
            return;
        }
        $quality = min(100, max(0, $quality ?? 100));
        file_put_contents($imagePath, decode($this->buffer)->encode($encodedFormat, $quality));
    }

    /**
     * Returns the image in a format suitable for sending to a client for parsing.
     *
     * @return BytesInput Bytes input for the image.
     */
    public function asInputSource(): BytesInput
    {
        return new BytesInput($this->buffer, $this->filename);
    }

    /**
     * Get the encoded image format, as understood by bernard_ledit.
     *
     * @param string $saveFormat Format to save the file as.
     * @return string Encoded image format.
     */
    public static function getEncodedImageFormat(string $saveFormat): string
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
