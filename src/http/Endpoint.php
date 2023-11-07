<?php

namespace Mindee\http;

use Mindee;

/**
 * Endpoint management.
 */
class Endpoint
{
    public const MINDEE_API_URL = 'https://api.mindee.net/v1';
    protected $apiKey;
    protected $urlRoot;
    public string $name;
    public string $owner;
    public string $version;

    public function __construct(string $api_key, string $name, string $owner, string $version)
    {
        $this->apiKey = $api_key;
        $this->name = $name;
        $this->owner = $owner;
        $this->version = $version;
        $this->urlRoot = self::MINDEE_API_URL."/products/$this->owner/$this->name/$this->version/";
    }

    public function predictRequestPost(
        \CURLFile $file_curl,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): array {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
            "Authorization: Token $this->apiKey",
            ]
        );

        curl_setopt($ch, CURLOPT_URL, $this->urlRoot.'/predict');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['document' => $file_curl]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v'.Mindee\VERSION);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    public function predictAsyncRequestPost(
        \CURLFile $file_curl,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): array {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
            "Authorization: Token $this->apiKey",
            ]
        );

        curl_setopt($ch, CURLOPT_URL, $this->urlRoot.'/predict');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['document' => $file_curl]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v'.Mindee\VERSION);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
}
