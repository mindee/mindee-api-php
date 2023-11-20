<?php

namespace Mindee\Input;

use Mindee\Error\MindeeMimeTypeException;

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
        if (!in_array($this->fileMimetype, ALLOWED_MIME_TYPES)) {
            $file_types = implode(', ', ALLOWED_MIME_TYPES);
            throw new MindeeMimeTypeException("File type " . $this->fileMimetype . " not allowed, must be one of $file_types.");
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
