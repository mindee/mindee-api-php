<?php

namespace Mindee\Api;

const VERSION = '0.1.0';

class Client
{
    public function docFromPath(string $mime_type, string $file_path): \CURLFile
    {
        return new \CURLFile($file_path, $mime_type, 'file.pdf');
    }

    public function docFromBytes(string $mime_type, string $file_bytes): \CURLFile
    {
        $file_b64 = "data://$mime_type;base64," . base64_encode($file_bytes);
        return new \CURLFile($file_b64, $mime_type, 'file.pdf');
    }

    public function predict(string $apiKey, $file_curl): array
    {
        $endpoint = new Endpoint($apiKey);
        return $endpoint->predict($file_curl);
    }
}
