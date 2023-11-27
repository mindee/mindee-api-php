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
    /**
     * @var string Url (name) of then endpoint.
     */
    public string $urlName;
    /**
     * @var string Name of the endpoint's owner.
     */
    public string $owner;
    /**
     * @var string Version of the endpoint.
     */
    public string $version;

    /**
     * @param string                 $url_name Url (name) of the endpoint.
     * @param string                 $owner    Name of the endpoint's owner.
     * @param string                 $version  Version of the endpoint.
     * @param \Mindee\Http\MindeeApi $settings Settings for the endpoint.
     */
    public function __construct(
        string $url_name,
        string $owner,
        string $version,
        MindeeApi $settings
    ) {
        parent::__construct($settings);
        $this->urlName = $url_name;
        $this->owner = $owner;
        $this->version = $version;
    }

    /**
     * Starts a CURL session, using GET.
     *
     * @param string $queue_id ID of the queue to poll.
     * @return array
     */
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
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

    /**
     * Starts a CURL session, using POST.
     *
     * @param \Mindee\Input\InputSource $file_curl     File to upload.
     * @param boolean                   $include_words Whether to include the full text for each page.
     *                      This performs a full OCR operation on the server and will increase response time.
     * @param boolean                   $cropper       Whether to include cropper results for each page.
     *                            This performs a cropping operation on the server and will increase response time.
     * @param boolean                   $async         Whether the query is in async mode.
     * @return array
     */
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
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

    /**
     * Retrieves a document from its queue ID.
     *
     * @param string $queue_id ID of the queue to poll.
     * @return array
     */
    public function documentQueueReqGet(string $queue_id): array
    {
        return $this->initCurlSessionGet($queue_id);
    }

    /**
     * Sends a document for asynchronous enqueuing.
     *
     * @param \Mindee\Input\InputSource $file_curl     File to upload.
     * @param boolean                   $include_words Whether to include the full text for each page.
     *                      This performs a full OCR operation on the server and will increase response time.
     * @param boolean                   $close_file    Whether to close the file after parsing it.
     * Not in use at the moment.
     * @param boolean                   $cropper       Whether to include cropper results for each page.
     *                            This performs a cropping operation on the server and will increase response time.
     * @return array
     */
    public function predictRequestPost(
        InputSource $file_curl,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($file_curl, $include_words, $cropper, false);
    }

    /**
     * Sends a document for synchronous enqueuing.
     *
     * @param InputSource $file_curl     File to upload.
     * @param boolean     $include_words Whether to include the full text for each page.
     *         This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $close_file    Whether to close the file after parsing it. Not in use at the moment.
     * @param boolean     $cropper       Whether to include cropper results for each page.
     *               This performs a cropping operation on the server and will increase response time.
     * @return array
     */
    public function predictAsyncRequestPost(
        InputSource $file_curl,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($file_curl, $include_words, $cropper, true);
    }

    /**
     * @param InputSource $file_curl
     * @param bool $include_words
     * @param bool $cropper
     * @return array
     */
}
