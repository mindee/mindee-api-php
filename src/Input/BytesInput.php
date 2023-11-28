<?php

namespace Mindee\Input;

/**
 * Raw bytes input.
 */
class BytesInput extends LocalInputSource
{
    /**
     * @param string $file_bytes Raw data as bytes.
     * @param string $file_name  File name of the input.
     */
    public function __construct(string $file_bytes, string $file_name)
    {
        $file_b64 = 'data://application/pdf;base64,' . base64_encode($file_bytes);
        $file = finfo_open();
        $mime_type = finfo_buffer($file, base64_decode($file_b64), FILEINFO_MIME_TYPE);
        $tmpfname = tempnam(sys_get_temp_dir(), 'bytes_');
        file_put_contents($tmpfname, $file_b64);
        $this->fileObject = new \CURLFile($file_b64, $mime_type, $file_name);
        unlink($tmpfname);
        parent::__construct();
    }
}
