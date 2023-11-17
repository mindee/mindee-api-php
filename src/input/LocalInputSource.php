<?php

namespace Mindee\input;

use Mindee\error\MindeeMimeTypeException;

const ALLOWED_MIME_TYPES = [
    'application/pdf',
    'image/heic',
    'image/png',
    'image/jpg',
    'image/jpeg',
    'image/tiff',
    'image/webp',
];

class LocalInputSource extends InputSource
{
    public \CURLFile $fileObject;
    public string $fileName;
    public string $fileMimetype;
    public ?string $filePath;

    private function checkMimeType()
    {
        if (!array_key_exists($this->fileMimetype, ALLOWED_MIME_TYPES)) {
            $file_types = implode(', ', ALLOWED_MIME_TYPES);
            throw new MindeeMimeTypeException("File type not allowed, must be one of $file_types.");
        }
    }

    public function __construct()
    {
        $this->checkMimeType();
    }

    public function isPDF(): bool
    {
        return $this->fileMimetype == 'application/pdf';
    }

    public function countDocPages() // TODO: add PDF lib
    {
    }

    public function processPDF() // TODO: add PDF lib
    {
    }

    public function mergePDFPages() // TODO: add PDF lib
    {
    }

    public function isPDFEmpty() // TODO: add PDF lib
    {
    }
}

class PathInput extends LocalInputSource
{
    public function __construct($file_path)
    {
        $this->filePath = basename($file_path);
        $this->fileName = $file_path;

        $file = finfo_open();
        $mime_type = mime_content_type($file_path);
        $this->fileObject = new \CURLFile($file, $this->$mime_type, $file_path);
        parent::__construct();
    }
}

class FileInput extends LocalInputSource
{
    public function __construct($file)
    {
        $this->filePath = $file->getPathName();
        $this->fileName = $file->getBaseName();
        $mime_type = mime_content_type($this->filePath);

        $this->fileObject = new \CURLFile($file, $this->fileName, $this->$mime_type);
        parent::__construct();
    }
}

class BytesInput extends LocalInputSource
{
    public function __construct($file_bytes, $file_name)
    {
        $file_b64 = 'data://application/pdf;base64,'.base64_encode($file_bytes);
        $file = finfo_open();
        $mime_type = finfo_buffer($file, base64_decode($file_b64), FILEINFO_MIME_TYPE);
        $tmpfname = tempnam(sys_get_temp_dir(), 'bytes_');
        file_put_contents($tmpfname, $file_b64);
        $this->fileObject = new \CURLFile($file_b64, $mime_type, $file_name);
        unlink($tmpfname);
    }
}

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
    }
}
