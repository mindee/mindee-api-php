<?php

namespace Mindee\Input;

/**
 * Local path input.
 */
class PathInput extends LocalInputSource
{
    /**
     * @param string $file_path Path to open.
     */
    public function __construct(string $file_path)
    {
        $this->filePath = $file_path;
        $this->fileName = basename($file_path);

        $file = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file, $this->filePath);
        $this->fileMimetype = $mime_type;
        $this->fileObject = new \CURLFile($this->filePath, $mime_type, $this->fileName);
        finfo_close($file);
        parent::__construct();
    }
}
