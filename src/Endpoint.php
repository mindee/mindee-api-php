<?php

namespace Mindee\Api;


class Endpoint
{
    const MINDEE_API_URL = "https://api.mindee.net/v1";
    protected $apiKey;
    protected $urlRoot;

    function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->urlRoot = self::MINDEE_API_URL . "/products/mindee/invoices/v3/";
    }

    public function predict($file_curl): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Token $this->apiKey",
        ]);

        curl_setopt($ch, CURLOPT_URL, $this->urlRoot . '/predict');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['document' => $file_curl]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "mindee-api-php@v" . VERSION);

        $resp = Array(
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch,  CURLINFO_HTTP_CODE)
        );
        curl_close($ch);

        return $resp;
    }
}
