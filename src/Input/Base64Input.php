<?php

namespace Mindee\Input;

/**
 * Base64-encoded text input.
 */
class Base64Input extends LocalInputSource
{
    /**
     * @param string $file_b64  Raw data as a base64-encoded string.
     * @param string $file_name File name of the input.
     */
    public function __construct(string $file_b64, string $file_name)
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
