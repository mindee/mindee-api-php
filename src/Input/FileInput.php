<?php

namespace Mindee\Input;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeSourceException;

/**
 * Binary file input.
 */
class FileInput extends LocalInputSource
{
    /**
     * @var mixed $file A file-like object compatible with CURLFile.
     */
    private mixed $file;

    /**
     * @param mixed &$file File reference.
     */
    public function __construct(mixed &$file)
    {
        $this->file = &$file;
        $this->filePath = stream_get_meta_data($this->file)['uri'];
        $this->fileName = basename($this->filePath);
        $this->fileMimetype = mime_content_type($this->filePath);
        $this->fileObject = new \CURLFile($this->filePath, $this->fileName, $this->fileMimetype);
        parent::__construct();
    }

    /**
     * Reads the contents of the file.
     *
     * @return array
     */
    public function readContents(): array
    {
        $fileContents = fread($this->file, filesize($this->filePath));
        return [$this->fileName, $fileContents];
    }

    /**
     * Returns the reference to the file object. Only used for testing purposes.
     *
     * @return mixed
     */
    public function getFilePtr()
    {
        return $this->file;
    }
}
