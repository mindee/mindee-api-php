<?php

namespace Mindee\Input;

use Mindee\Error\MindeeSourceException;

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
     * @param mixed   &$file  File reference.
     * @param boolean $fixPDF Whether the PDF should be fixed or not.
     */
    public function __construct(&$file, bool $fixPDF = false)
    {
        $this->file = &$file;
        $this->filePath = stream_get_meta_data($this->file)['uri'];
        $this->fileName = basename($this->filePath);
        $this->fileMimetype = mime_content_type($this->filePath);
        $this->fileObject = new \CURLFile($this->filePath, $this->fileName, $this->fileMimetype);
        parent::__construct($fixPDF);
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
     * Closes the file.
     *
     * @return void
     * @throws MindeeSourceException Throws when strict mode is enabled.
     */
    public function close(): void
    {
        if (!is_resource($this->file)) {
            if ($this->throwsOnClose) {
                throw new MindeeSourceException("File is already closed.");
            }
            error_log("File is already closed.");
        } else {
            fclose($this->file);
        }
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
