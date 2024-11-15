<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;

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
    public function executeWorkflowRequestPost(
        InputSource $fileCurl,
        bool $includeWords,
        bool $fullText,
        bool $closeFile,
        bool $cropper
    ): array {
        return $this->initCurlSessionPost($fileCurl, $includeWords, $fullText, $cropper, 'workflow', $closeFile);
    }
}
