<?php

declare(strict_types=1);

namespace Mindee\Input;

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
     */
    public function __construct(&$file)
    {
        $this->file = &$file;
        $this->filePath = stream_get_meta_data($this->file)['uri'];
        $this->fileName = basename($this->filePath);
        $this->fileMimetype = mime_content_type($this->filePath);
        $this->fileObject = new CURLFile($this->filePath, $this->fileName, $this->fileMimetype);
        parent::__construct();
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
