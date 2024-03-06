<?php

/**
 * Local input handling.
 */

namespace Mindee\Input;

use CURLFile;
use Mindee\Error\MindeeMimeTypeException;
use Mindee\Error\MindeeSourceException;

/**
 * List of allowed mime types for document parsing.
 */
const ALLOWED_MIME_TYPES = [
    'application/pdf',
    'image/heic',
    'image/png',
    'image/jpg',
    'image/jpeg',
    'image/tiff',
    'image/webp',
];

/**
 * Base class for all input sources coming from the local machine.
 */
abstract class LocalInputSource extends InputSource
{
    /**
     * @var CURLFile File object, as a CURLFile for simplicity.
     */
    public CURLFile $fileObject;
    /**
     * @var string Name of the file, mandatory for proper Mime type handling server-side.
     */
    public string $fileName;
    /**
     * @var string File Mime type, as a string.
     */
    public string $fileMimetype;
    /**
     * @var string|null Path of the file for files retrieved from a path.
     */
    public ?string $filePath;

    /**
     * @var boolean Sets the input mode to debug. Only used in unit tests.
     */
    protected bool $throwsOnClose;
    /**
     * Checks the mimetype integrity of a file.
     *
     * @return void
     * @throws MindeeMimeTypeException Throws if the Mime type isn't allowed.
     */
    private function checkMimeType()
    {
        if (!in_array($this->fileMimetype, ALLOWED_MIME_TYPES)) {
            $fileTypes = implode(', ', ALLOWED_MIME_TYPES);
            throw new MindeeMimeTypeException(
                "File type " .
                $this->fileMimetype .
                " not allowed, must be one of $fileTypes."
            );
        }
    }

    /**
     * Base constructor, mostly used for Mime type checking.
     */
    public function __construct()
    {
        $this->checkMimeType();
        $this->throwsOnClose = false;
    }

    /**
     * Checks whether the file type is a PDF.
     *
     * @return boolean
     */
    public function isPDF(): bool
    {
        return $this->fileMimetype == 'application/pdf';
    }

    /**
     * Counts the amount of pages in a PDF.
     * To be implemented.
     *
     * @return void
     */
    public function countDocPages() // TODO: add PDF lib
    {
    }

    /**
     * Processes a PDF file.
     * To be implemented.
     *
     * @return void
     */
    public function processPDF() // TODO: add PDF lib
    {
    }

    /**
     * Merges the pages of a PDF.
     * To be implemented.
     *
     * @return void
     */
    public function mergePDFPages() // TODO: add PDF lib
    {
    }

    /**
     * Checks whether the contents of a PDF are empty.
     * To be implemented.
     *
     * @return void
     */
    public function isPDFEmpty() // TODO: add PDF lib
    {
    }

    /**
     * Reads the contents of the file.
     *
     * @return array
     */
    public function readContents(): array
    {
        $fileHandle = fopen($this->fileObject->getFilename(), 'rb');
        $strContents = fread($fileHandle, filesize($this->fileObject->getFilename()));
        fclose($fileHandle);
        return [basename($this->fileObject->getFilename()), $strContents];
    }

    /**
     * Closes the handle/stream, if the input type supports it.
     *
     * @return void
     * @throws MindeeSourceException Throws when strict mode is enabled.
     */
    public function close(): void
    {
        if ($this->throwsOnClose) {
            throw new MindeeSourceException("Closing is not implemented on this type of local input source.");
        } else {
            error_log("Closing is not implemented on this type of local input source.");
        }
    }

    /**
     * Enables strict mode.
     * Currently only used to throw on misuse of close().
     *
     * @return void
     */
    public function enableStrictMode()
    {
        $this->throwsOnClose = true;
    }
}
