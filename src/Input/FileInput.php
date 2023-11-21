<?php

namespace Mindee\Input;

class FileInput extends LocalInputSource
{
    public function __construct($file)
    {
        $this->filePath = $file->getPathName();
        $this->fileName = $file->getBaseName();
        $mime_type = mime_content_type($this->filePath);

        $this->fileObject = new \CURLFile($file, $this->fileName, $this->$mime_type);
        parent::__construct();
    }
}
