<?php

namespace Mindee\Input;

/**
 * Raw bytes input.
 */
class BytesInput extends LocalInputSource
{
    /**
     * @param string $fileBytes Raw data as bytes.
     * @param string $fileName  File name of the input.
     */
    public function __construct(string $fileBytes, string $fileName)
    {
        $fileB64 = 'data://application/pdf;base64,' . base64_encode($fileBytes);
        $file = finfo_open();
        $mimeType = finfo_buffer($file, base64_decode($fileB64), FILEINFO_MIME_TYPE);
        $tmpfname = tempnam(sys_get_temp_dir(), 'bytes_');
        file_put_contents($tmpfname, $fileB64);
        $this->fileObject = new \CURLFile($fileB64, $mimeType, $fileName);
        unlink($tmpfname);
        parent::__construct();
    }
}
