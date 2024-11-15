<?php

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\Http;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeException;

/**
 * Data class containing settings for workflows.
 */
class MindeeWorkflowApi extends BaseApi
{
    /**
     * @var string ID of the workflow.
     */
    public string $workflowId;
    /**
     * @param string|null $apiKey     API key.
     * @param string      $workflowId ID of the workflow.
     * @throws MindeeException Throws if the API key specified is invalid.
     */
    public function __construct(
        ?string $apiKey,
        string $workflowId
    ) {
        parent::__construct($apiKey);
        if (!$this->apiKey || strlen($this->apiKey) == 0) {
            throw new MindeeException(
                "Missing API key. Please check your Client configuration.You can set this using the " .
                API_KEY_ENV_NAME . ' environment variable.',
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $this->workflowId = $workflowId;
        $this->urlRoot = rtrim(
            $this->baseUrl,
            "/"
        ) . "/workflows/$this->workflowId/executions";
    }
}
