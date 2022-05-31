<?php

namespace Mindee\Api;

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

    public function predict(string $token, $file_curl): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Token $token",
        ]);

        curl_setopt($ch, CURLOPT_URL, 'https://api.mindee.net/v1/products/mindee/invoices/v3/predict');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['document' => $file_curl]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $resp = Array(
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch,  CURLINFO_HTTP_CODE)
        );
        curl_close($ch);

        return $resp;
    }
}
