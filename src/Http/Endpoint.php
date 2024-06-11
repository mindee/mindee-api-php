<?php

namespace Mindee\Http;

use Mindee;
use Mindee\Input\InputSource;
use Mindee\Input\URLInputSource;
use Mindee\Input\LocalInputSource;

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
     * @param string    $urlName  Url (name) of the endpoint.
     * @param string    $owner    Name of the endpoint's owner.
     * @param string    $version  Version of the endpoint.
     * @param MindeeApi $settings Settings for the endpoint.
     */
    public function __construct(
        string $urlName,
        string $owner,
        string $version,
        MindeeApi $settings
    ) {
        parent::__construct($settings);
        $this->urlName = $urlName;
        $this->owner = $owner;
        $this->version = $version;
    }

    /**
     * Starts a CURL session, using GET.
     *
     * @param string $queueId ID of the queue to poll.
     * @return array
     */
    private function initCurlSessionGet(string $queueId): array
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . "/documents/queue/$queueId");
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
     * @param InputSource $fileCurl     File to upload.
     * @param boolean     $includeWords Whether to include the full text for each page.
     *                    This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                    This performs a cropping operation on the server and will increase response time.
     * @param boolean     $async        Whether the query is in async mode.
     * @param boolean     $closeFile    Close file.
     * @return array
     */
    private function initCurlSessionPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $cropper,
        bool $async,
        bool $closeFile
    ): array {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        $suffix = $async ? '/predict_async' : '/predict';
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($fileCurl instanceof URLInputSource) {
            $postFields = ['document' => $fileCurl->url];
        } elseif ($fileCurl instanceof LocalInputSource) {
            if ($closeFile) {
                $fileCurl->close();
            }
            $postFields = ['document' => $fileCurl->fileObject];
        }
        if ($includeWords) {
            $postFields['include_mvision'] = 'true';
        }
        if ($cropper) {
            $suffix .= '?cropper=true';
        }
        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . $suffix);
        if (isset($postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
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
     * @param string $queueId ID of the queue to poll.
     * @return array
     */
    public function documentQueueReqGet(string $queueId): array
    {
        return $this->initCurlSessionGet($queueId);
    }

    /**
     * Sends a document for asynchronous enqueuing.
     *
     * @param InputSource $fileCurl     File to upload.
     * @param boolean     $includeWords Whether to include the full text for each page.
     *                    This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $closeFile    Whether to close the file after parsing it.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                    This performs a cropping operation on the server and will increase response time.
     * @return array
     */
    public function predictRequestPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $closeFile,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($fileCurl, $includeWords, $cropper, false, $closeFile);
    }

    /**
     * Sends a document for synchronous enqueuing.
     *
     * @param InputSource $fileCurl     File to upload.
     * @param boolean     $includeWords Whether to include the full text for each page.
     *                    This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $closeFile    Whether to close the file after parsing it.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                    This performs a cropping operation on the server and will increase response time.
     * @return array
     */
    public function predictAsyncRequestPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $closeFile,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($fileCurl, $includeWords, $cropper, true, $closeFile);
    }

    /**
     * @param InputSource $fileCurl
     * @param bool $includeWords
     * @param bool $cropper
     * @return array
     */
}
