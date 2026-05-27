<?php

declare(strict_types=1);

namespace Mindee\Input;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeSourceException;
use CURLFile;

/**
 * Binary file input.
 */
class FileInput extends LocalInputSource
{
    /**
     * @var resource $file A file resource compatible with CURLFile.
     */
    private $file;

    /**
     * @param resource &$file File reference.
     * @param boolean $fixPdf Whether to try to fix a broken PDF.
     */
    public function __construct(&$file, bool $fixPdf = false)
    {
        $this->file = &$file;
        $this->filePath = stream_get_meta_data($this->file)['uri'];
        $this->fileName = basename($this->filePath);
        $this->fileMimetype = mime_content_type($this->filePath);
        $this->fileObject = new CURLFile($this->filePath, $this->fileName, $this->fileMimetype);
        parent::__construct($fixPdf);
    }

    /**
     * Reads the contents of the file.
     * @return array{0: string, 1: string} File name and contents as a tuple.
     */
    public function readContents(): array
    {
        $fileContents = fread($this->file, filesize($this->filePath));
        return [$this->fileName, $fileContents];
    }
}
