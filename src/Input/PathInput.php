<?php

declare(strict_types=1);

namespace Mindee\Input;

use CURLFile;

/**
 * Local path input.
 */
class PathInput extends LocalInputSource
{
    /**
     * @param string $filePath Path to open.
     * @param boolean $fixPdf Whether to try to fix a broken PDF.
     */
    public function __construct(string $filePath, bool $fixPdf = false)
    {
        $this->filePath = $filePath;
        $this->fileName = basename($filePath);

        $file = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($file, $this->filePath);
        $this->fileMimetype = $mimeType;
        $this->fileObject = new CURLFile($this->filePath, $mimeType, $this->fileName);
        finfo_close($file);
        parent::__construct($fixPdf);
    }
}
