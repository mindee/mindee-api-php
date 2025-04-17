<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\URLInputSource;
use Mindee\Input\WorkflowOptions;

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
     * @param InputSource     $fileCurl        File to upload.
     * @param WorkflowOptions $workflowOptions Workflow options.
     * @return array
     */
    public function executeWorkflowRequestPost(
        InputSource $fileCurl,
        WorkflowOptions $workflowOptions
    ): array {
        return $this->initCurlSessionPost($fileCurl, $workflowOptions);
    }


    /**
     * Starts a CURL session using POST.
     *
     * @param InputSource     $fileCurl        File to upload.
     * @param WorkflowOptions $workflowOptions Workflow options.
     * @return array
     */
    private function initCurlSessionPost(
        InputSource $fileCurl,
        WorkflowOptions $workflowOptions
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
        if (!empty($workflowOptions->alias)) {
            $postFields['alias'] = $workflowOptions->alias;
        }
        if (!empty($workflowOptions->publicUrl)) {
            $postFields['public_url'] = $workflowOptions->publicUrl;
        }
        if (!empty($workflowOptions->priority)) {
            $postFields['priority'] = $workflowOptions->priority;
        }
        $params = [];

        if ($workflowOptions->fullText) {
            $params['full_text_ocr'] = 'true';
        }

        if ($workflowOptions->rag) {
            $params['rag'] = 'true';
        }

        if (!empty($params)) {
            $suffix .= '?' . http_build_query($params);
        }
        return $this->setFinalCurlOpts($ch, $suffix, $postFields);
    }
}
