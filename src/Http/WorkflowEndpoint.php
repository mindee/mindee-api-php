<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\URLInputSource;

/**
 * Workflow router endpoint substitute.
 */
class WorkflowEndpoint extends BaseEndpoint
{
    /**
     * @param MindeeWorkflowApi $settings Settings for the endpoint.
     */
    public function __construct(
        MindeeWorkflowApi $settings
    ) {
        parent::__construct($settings);
    }

    /**
     * Sends a document for synchronous enqueuing.
     *
     * @param InputSource $fileCurl  File to upload.
     * @param string|null $alias     Alias to give to the document.
     * @param string|null $priority  Priority to give to the document.
     * @param boolean     $fullText  Whether to include the full OCR text response in compatible APIs.
     *                               This performs a full OCR operation on the server and may increase response time.
     *                               This performs a cropping operation on the server and will increase response time.
     * @param string|null $publicUrl One time use encrypted URL for authentication.
     * @return array
     */
    public function executeWorkflowRequestPost(
        InputSource $fileCurl,
        ?string $alias,
        ?string $priority,
        bool $fullText,
        ?string $publicUrl
    ): array {
        return $this->initCurlSessionPost($fileCurl, $alias, $priority, $fullText, $publicUrl);
    }


    /**
     * Starts a CURL session, using POST.
     *
     * @param InputSource $fileCurl    File to upload.
     * @param string|null $alias       Whether to include the full text for each page.
     * @param string|null $priority    Whether to include the full text for each page.
     * @param boolean     $fullTextOcr Whether to include the full OCR text response in compatible APIs.
     *                              This performs a full OCR operation on the server and may increase response time.
     * @param string|null $publicUrl   One time use encrypted URL for authentication.
     * @return array
     */
    private function initCurlSessionPost(
        InputSource $fileCurl,
        ?string $alias,
        ?string $priority,
        bool $fullTextOcr,
        ?string $publicUrl,
        bool $rag
    ): array {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );
        $postFields = null;
        $suffix = '';
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($fileCurl instanceof URLInputSource) {
            $postFields = ['document' => $fileCurl->url];
        } elseif ($fileCurl instanceof LocalInputSource) {
            $postFields = ['document' => $fileCurl->fileObject];
        }
        if (!empty($alias)) {
            $postFields['alias'] = $alias;
        }
        if (!empty($publicUrl)) {
            $postFields['public_url'] = $publicUrl;
        }
        if (!empty($priority)) {
            $postFields['priority'] = $priority;
        }
        if ($fullTextOcr && !$rag) {
            $suffix .= '?full_text_ocr=true';
        } elseif ($rag && !$fullTextOcr) {
            $suffix .= '?rag=true';
        } elseif ($rag && $fullTextOcr) {
            $suffix .= '?full_text_ocr=true&rag=true';
        }
        return $this->setFinalCurlOpts($ch, $suffix, $postFields);
    }
}
