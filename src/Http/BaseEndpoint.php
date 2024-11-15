<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\URLInputSource;

use const Mindee\VERSION;

/**
 * Abstract class for endpoints.
 */
abstract class BaseEndpoint
{
    /**
     * @var MindeeApi|MindeeWorkflowApi Settings of the endpoint.
     */
    public $settings;

    /**
     * @param MindeeApi|MindeeWorkflowApi $settings Input settings.
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Starts a CURL session, using GET.
     *
     * @param string $queueId ID of the queue to poll.
     * @return array
     */
    protected function initCurlSessionGet(string $queueId): array
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
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v' . VERSION);

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
     *                        This performs a full OCR operation on the server and will increase response time.
     * @param boolean     $fullText     Whether to include the full OCR text response in compatible APIs.
     *                             This performs a full OCR operation on the server and may increase response time.
     * @param boolean     $cropper      Whether to include cropper results for each page.
     *                             This performs a cropping operation on the server and will increase response time.
     * @param string      $parseType    Whether the query is in async/sync or workflow.
     * @param boolean     $closeFile    Close file.
     * @return array
     */
    protected function initCurlSessionPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $fullText,
        bool $cropper,
        string $parseType,
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

        $suffix = '';
        switch ($parseType) {
            case 'async':
                $suffix = '/predict_async';
                break;
            case 'workflow':
                break;
            case 'sync':
            default:
                $suffix = '/predict';
                break;
        }
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
        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . $suffix);
        if (isset($postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v' . VERSION);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
}
