<?php

namespace Mindee\Input;

class BytesInput extends LocalInputSource
{
    public function __construct($file_bytes, $file_name)
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
