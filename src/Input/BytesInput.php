<?php

namespace Mindee\Input;

/**
 * Raw bytes input.
 */
class BytesInput extends LocalInputSource
{
    /**
     * @var resource Stream object.
     */
    public $stream;

    /**
     * @param string $fileBytes Raw data as bytes.
     * @param string $fileName  File name of the input.
     */
    public function __construct(string $fileBytes, string $fileName)
    {
        $this->stream = fopen('php://temp', 'r+');
        fwrite($this->stream, $fileBytes);
        rewind($this->stream);
        $tmpfname = tempnam(sys_get_temp_dir(), 'bytes_');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->fileMimetype = finfo_buffer($finfo, $fileBytes);
        file_put_contents($tmpfname, $this->stream);
        $this->fileName = $fileName;
        $this->fileObject = new \CURLFile($tmpfname, $this->fileMimetype, $fileName);
        unlink($tmpfname);
        rewind($this->stream);
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
        rewind($this->stream);
        $streamContents = stream_get_contents($this->stream);
        if ($closeFile) {
            fclose($this->stream);
        } else {
            rewind($this->stream);
        }
        return [$this->fileName, $streamContents];
    }
}
