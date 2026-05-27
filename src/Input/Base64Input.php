<?php

declare(strict_types=1);

namespace Mindee\Input;

use CURLFile;

/**
 * Base64-encoded text input.
 */
class Base64Input extends LocalInputSource
{
    /**
     * @var string Temporary file.
     */
    private string $tempFile;

    /**
     * @param string $strBase64 Raw data as a base64-encoded string.
     * @param string $fileName File name of the input.
     * @param boolean $fixPdf Whether to try to fix a broken PDF.
     */
    public function __construct(string $strBase64, string $fileName, bool $fixPdf = false)
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'b64_');
        $this->fileName = $fileName;
        file_put_contents($this->tempFile, base64_decode($strBase64, true));
        rename($this->tempFile, $this->tempFile .= "." . pathinfo($this->fileName, PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->fileMimetype = finfo_buffer($finfo, base64_decode($strBase64, true));
        $this->fileObject = new CURLFile($this->tempFile, $this->fileMimetype, $this->fileName);
        parent::__construct($fixPdf);
    }


    /**
     * Reads the contents of the file.
     * @return array{0: string, 1: string} File name and contents as a tuple.
     */
    public function readContents(): array
    {
        $fileHandle = fopen($this->fileObject->getFilename(), 'r');
        $strContents = fread($fileHandle, filesize($this->fileObject->getFilename()));
        unlink($this->tempFile);
        return [basename($this->fileName), $strContents];
    }
}
