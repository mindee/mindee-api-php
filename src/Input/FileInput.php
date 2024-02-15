<?php

namespace Mindee\Input;

/**
 * Binary file input.
 */
class FileInput extends LocalInputSource
{
    /**
     * @var mixed $file A file-like object compatible with CURLFile.
     */
    private $file;

    /**
     * @param mixed &$file File reference.
     */
    public function __construct(&$file)
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
     * @param boolean $closeFile Whether to close the file after parsing it.
     * @return array
     */
    public function readContents(bool $closeFile = true): array
    {
        $fileContents = fread($this->file, filesize($this->filePath));
        if ($closeFile) {
            unlink($this->filePath);
        }
        return [$this->fileName, $fileContents];
    }
}
