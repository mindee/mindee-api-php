<?php

namespace Mindee\Input;
class Base64Input extends LocalInputSource
{
    public function __construct($file_b64, $file_name)
    {
        $file = finfo_open();
        $mime_type = finfo_buffer($file, base64_decode($file_b64), FILEINFO_MIME_TYPE);
        $tmpfname = tempnam(sys_get_temp_dir(), 'b64_');
        file_put_contents($tmpfname, $file_b64);
        $this->fileObject = new \CURLFile($file_b64, $mime_type, $file_name);
        unlink($tmpfname);
        parent::__construct();
    }
}