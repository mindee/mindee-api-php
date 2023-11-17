<?php

namespace Mindee\http;

use Mindee;
use Mindee\input\InputSource;
use Mindee\input\URLInputSource;

/**
 * Endpoint management.
 */
class Endpoint extends BaseEndpoint
{
    public string $urlName;
    public string $owner;
    public string $version;

    public function __construct(
        string    $url_name,
        string    $owner,
        string    $version,
        MindeeApi $settings
    )
    {
        parent::__construct($settings);
        $this->urlName = $url_name;
        $this->owner = $owner;
        $this->version = $version;
    }

    private function initCurlSession(InputSource $file_curl, bool $include_words, bool $cropper): array
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . '/predict');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($file_curl instanceof URLInputSource) {
            $post_fields = ['document' => $file_curl];
        } else {
            $post_fields = ['document' => $file_curl->fileObject];
        }
        if ($include_words) {
            $post_fields['include_mvision'] = 'true';
        }
        if ($cropper) {
            $post_fields['cropper'] = 'true';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v' . Mindee\VERSION);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
    public function predictRequestPost(
        InputSource $file_curl,
        bool        $include_words,
        bool        $close_file,
        bool        $cropper
    ): array
    {
        return $this->initCurlSession($file_curl, $include_words, $cropper);
    }

    public function predictAsyncRequestPost(
        $file_curl,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): array
    {
        return $this->initCurlSession($file_curl, $include_words, $cropper);
    }

    /**
     * @param InputSource $file_curl
     * @param bool $include_words
     * @param bool $cropper
     * @return array
     */
}
