<?php

namespace Mindee\Input;

/**
 * Binary file input.
 */
class FileInput extends LocalInputSource
{
    /**
     * @param mixed $file A file-like object compatible with CURLFile.
     */
    public function __construct($file)
    {
        $this->filePath = $file->getPathName();
        $this->fileName = $file->getBaseName();
        $mimeType = mime_content_type($this->filePath);

        $this->fileObject = new \CURLFile($file, $this->fileName, $this->$mimeType);
        parent::__construct();
    }
}
