<?php

namespace Mindee\Http;

use Mindee;
use Mindee\Input\InputSource;
use Mindee\Input\URLInputSource;

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

    private function initCurlSessionGet(string $queue_id): array
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . "/documents/queue/$queue_id");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v' . Mindee\VERSION);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    private function initCurlSessionPost(InputSource $file_curl, bool $include_words, bool $cropper, bool $async): array
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        $suffix = $async ? '/predict_async' : '/predict';
        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . $suffix);
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

    public function documentQueueReqGet(string $queue_id): array
    {
        return $this->initCurlSessionGet($queue_id);
    }

    public function predictRequestPost(
        InputSource $file_curl,
        bool        $include_words,
        bool        $close_file,
        bool        $cropper
    ): array
    {
        return $this->initCurlSessionPost($file_curl, $include_words, $cropper, false);
    }

    public function predictAsyncRequestPost(
        $file_curl,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): array
    {
        return $this->initCurlSessionPost($file_curl, $include_words, $cropper, true);
    }

    /**
     * @param InputSource $file_curl
     * @param bool $include_words
     * @param bool $cropper
     * @return array
     */
}
