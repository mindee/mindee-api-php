<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\URLInputSource;

use const Mindee\VERSION;

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
     * Starts a CURL session, using POST.
     *
     * @param InputSource $fileCurl     File to upload.
     * @param boolean     $includeWords Whether to include the full text for each page.
     *                        This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $fullText     Whether to include the full OCR text response in compatible APIs.
     *                             This performs a full OCR operation on the server and may increase response time.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                             This performs a cropping operation on the server and will increase response time.
     * @param boolean     $async        Whether the query is in async mode.
     * @param boolean     $closeFile    Close file.
     * @return array
     */
    private function initCurlSessionPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $fullText,
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
        $postFields = null;
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
        if ($fullText && $cropper) {
            $suffix .= '?full_text_ocr=true&cropper=true';
        } else {
            if ($fullText) {
                $suffix .= '?full_text_ocr=true';
            }
            if ($cropper) {
                $suffix .= '?cropper=true';
            }
        }
        return $this->setFinalCurlOpts($ch, $suffix, $postFields);
    }
}
