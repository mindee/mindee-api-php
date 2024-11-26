<?php

namespace Mindee\Parsing\Common;

use Exception;
use Mindee\Product\Generated\GeneratedV1;

/**
 * Represents the server response after a document is sent to a workflow.
 */
class WorkflowResponse extends ApiResponse
{
    /**
     * @var Execution Result of the base inference.
     */
    public Execution $execution;

    /**
     * @param string|null $predictionType Type of prediction.
     * @param array       $rawResponse    Raw HTTP response.
     * @throws Exception Throws if the prediction type isn't recognized or if a field can't be deserialized.
     */
    public function __construct(?string $predictionType, array $rawResponse)
    {
        parent::__construct($rawResponse);
        $predictionType ??= GeneratedV1::class;
        $this->execution = new Execution($predictionType, $rawResponse['execution']);
    }
}
