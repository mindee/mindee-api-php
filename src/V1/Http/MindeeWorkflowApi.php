<?php

declare(strict_types=1);

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\V1\Http;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeException;

/**
 * Data class containing settings for workflows.
 */
class MindeeWorkflowApi extends BaseApi
{
    /**
     * @param string|null $apiKey API key.
     * @param string $workflowId ID of the workflow.
     * @throws MindeeException Throws if the API key specified is invalid.
     */
    public function __construct(
        ?string $apiKey,
        public string $workflowId
    ) {
        parent::__construct($apiKey);
        if (empty($this->apiKey)) {
            throw new MindeeException(
                "Missing API key. Please check your Client configuration.You can set this using the "
                . API_KEY_ENV_NAME . ' environment variable.',
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $this->urlRoot = rtrim(
            $this->baseUrl,
            "/"
        ) . "/workflows/$this->workflowId/executions";
    }
}
