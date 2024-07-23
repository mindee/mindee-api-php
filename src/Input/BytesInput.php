<?php

namespace Mindee\Input;

/**
 * Raw bytes input.
 */
class BytesInput extends LocalInputSource
{
    /**
     * @var string Temporary file.
     */
    private string $tempFile;

    /**
     * @param string  $fileBytes Raw data as bytes.
     * @param string  $fileName  File name of the input.
     * @param boolean $fixPDF    Whether the PDF should be fixed or not.
     */
    public function __construct(string $fileBytes, string $fileName, bool $fixPDF = false)
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'b64_');
        $this->fileName = $fileName;
        file_put_contents($this->tempFile, $fileBytes);
        rename($this->tempFile, $this->tempFile .= "." . pathinfo($this->fileName, PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->fileMimetype = finfo_buffer($finfo, $fileBytes);
        $this->fileObject = new \CURLFile($this->tempFile, $this->fileMimetype, $this->fileName);
        parent::__construct($fixPDF);
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
        return [$this->fileName, $strContents];
    }
}
