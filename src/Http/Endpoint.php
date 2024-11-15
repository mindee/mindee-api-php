<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;

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
     *                        This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $fullText     Whether to include the full OCR text response in compatible APIs.
     *                             This performs a full OCR operation on the server and may increase response time.
     * @param boolean     $closeFile    Whether to close the file after parsing it.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                             This performs a cropping operation on the server and will increase response time.
     * @return array
     */
    public function predictRequestPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $fullText,
        bool $closeFile,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($fileCurl, $includeWords, $fullText, $cropper, 'sync', $closeFile);
    }

    /**
     * Sends a document for synchronous enqueuing.
     *
     * @param InputSource $fileCurl     File to upload.
     * @param boolean     $includeWords Whether to include the full text for each page.
     *                        This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $fullText     Whether to include the full OCR text response in compatible APIs.
*                             This performs a full OCR operation on the server and may increase response time.
     * @param boolean     $closeFile    Whether to close the file after parsing it.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                             This performs a cropping operation on the server and will increase response time.
     * @return array
     */
    public function predictAsyncRequestPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $fullText,
        bool $closeFile,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($fileCurl, $includeWords, $fullText, $cropper, 'async', $closeFile);
    }

    /**
     * @param InputSource $fileCurl
     * @param bool $includeWords
     * @param bool $cropper
     * @return array
     */
}
