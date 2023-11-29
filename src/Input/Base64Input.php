<?php

namespace Mindee\Input;

/**
 * Base64-encoded text input.
 */
class Base64Input extends LocalInputSource
{
    /**
     * @param string $fileB64  Raw data as a base64-encoded string.
     * @param string $fileName File name of the input.
     */
    public function __construct(string $fileB64, string $fileName)
    {
        $file = finfo_open();
        $mimeType = finfo_buffer($file, base64_decode($fileB64), FILEINFO_MIME_TYPE);
        $tmpfname = tempnam(sys_get_temp_dir(), 'b64_');
        file_put_contents($tmpfname, $fileB64);
        $this->fileObject = new \CURLFile($fileB64, $mimeType, $fileName);
        unlink($tmpfname);
        parent::__construct();
    }
}
