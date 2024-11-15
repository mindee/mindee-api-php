<?php

namespace Mindee\Parsing\Common;

use DateTimeImmutable;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeApiException;
use Mindee\Product\Generated\GeneratedV1Document;
use ReflectionClass;
use ReflectionException;

/**
 * Representation of a workflow execution.
 */
class Execution
{
    /**
     * Identifier for the batch to which the execution belongs.
     * @var string|null
     */
    public ?string $batchName;

    /**
     * The time at which the execution started.
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $createdAt;

    /**
     * File representation within a workflow execution.
     * @var ExecutionFile|null
     */
    public ?ExecutionFile $file;

    /**
     * Identifier for the execution.
     * @var string|null
     */
    public ?string $id;

    /**
     * Deserialized inference object.
     * @var Inference|null
     */
    public $inference;

    /**
     * Priority of the execution.
     * @var string|null
     */
    public ?string $priority;

    /**
     * The time at which the file was tagged as reviewed.
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $reviewedAt;

    /**
     * The time at which the file was uploaded to a workflow.
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $availableAt;

    /**
     * Reviewed fields and values.
     * @var GeneratedV1Document|null
     */
    public ?GeneratedV1Document $reviewedPrediction;

    /**
     * Execution Status.
     * @var string|null
     */
    public ?string $status;

    /**
     * Execution type.
     * @var string|null
     */
    public ?string $type;

    /**
     * The time at which the file was uploaded to a workflow.
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $uploadedAt;

    /**
     * Identifier for the workflow.
     * @var string|null
     */
    public ?string $workflowId;

    /**
     * @param string $predictionType Type of prediction.
     * @param array  $rawResponse    Raw execution array.
     * @throws Exception|MindeeApiException Throws if one of the objects can't properly be created.
     */
    public function __construct(string $predictionType, array $rawResponse)
    {
        $this->batchName = $rawResponse['batch_name'] ?? null;
        $this->createdAt = isset($rawResponse['created_at']) ? new DateTimeImmutable($rawResponse['created_at']) : null;
        $this->file = isset($rawResponse['file']) ? new ExecutionFile($rawResponse['file']) : null;
        $this->id = $rawResponse['id'] ?? null;
        if (isset($rawResponse['inference'])) {
            try {
                $reflection = new ReflectionClass($predictionType);
                $this->inference = $reflection->newInstance($rawResponse['inference']);
            } catch (ReflectionException $e) {
                throw new MindeeApiException(
                    "Unable to create custom product " . $predictionType,
                    ErrorCode::INTERNAL_LIBRARY_ERROR,
                    $e
                );
            }
        }
        $this->priority = $rawResponse['priority'] ?? null;
        $this->reviewedAt = isset($rawResponse['reviewed_at']) ?
            new DateTimeImmutable($rawResponse['reviewed_at']) : null;
        $this->availableAt = isset($rawResponse['available_at']) ?
            new DateTimeImmutable($rawResponse['available_at']) : null;
        $this->reviewedPrediction = isset($rawResponse['reviewed_prediction']) ?
            new GeneratedV1Document($rawResponse['reviewed_prediction']) : null;
        $this->status = $rawResponse['status'] ?? null;
        $this->type = $rawResponse['type'] ?? null;
        $this->uploadedAt = isset($rawResponse['uploaded_at']) ?
            new DateTimeImmutable($rawResponse['uploaded_at']) : null;
        $this->workflowId = $rawResponse['workflow_id'] ?? null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $objAsArray = get_object_vars($this);
        ksort($objAsArray);

        return json_encode($objAsArray, JSON_PRETTY_PRINT);
    }
}
