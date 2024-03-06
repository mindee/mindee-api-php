<?php

namespace Mindee\Input;

use Mindee\Error\MindeeApiException;

/**
 * Base64-encoded text input.
 */
class Base64Input extends LocalInputSource
{
    /**
     * @var string Temporary file.
     */
    private $tempFile;

    /**
     * @param string $fileB64  Raw data as a base64-encoded string.
     * @param string $fileName File name of the input.
     */
    public function __construct(string $fileB64, string $fileName)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->fileMimetype = finfo_buffer($finfo, base64_decode($fileB64));
        $this->tempFile = tempnam(sys_get_temp_dir(), 'b64_');
        $this->fileName = $fileName;
        file_put_contents($this->tempFile, $fileB64);
        $this->fileObject = new \CURLFile($this->tempFile, $this->fileMimetype, $fileName);
        parent::__construct();
    }


    /**
     * Reads the contents of the file.
     *
     * @return array
     */
    public function readContents(): array
    {
        $fileHandle = fopen($this->fileObject->getFilename(), 'r');
        $strContents = fread($fileHandle, filesize($this->fileObject->getFilename()));
        unlink($this->tempFile);
        return [basename($this->fileName), $strContents];
    }
}
