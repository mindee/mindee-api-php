<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

use DateTimeImmutable;
use Exception;
use Mindee\V2\Parsing\Error\ErrorResponse;

/**
 * Webhook payload returned when an inference fails before producing a result.
 */
class FailedInferenceResponse extends BaseResponse
{
    /**
     * @var string UUID of the failed inference.
     */
    public string $inferenceId;
    /**
     * @var string UUID of the model used.
     */
    public string $modelId;

    /**
     * @var string Name of the input file.
     */
    public string $fileName;

    /**
     * @var string Alias sent for the file, if any.
     */
    public string $fileAlias;

    /**
     * @var ErrorResponse Problem details for the failure, if available.
     */
    public ErrorResponse $error;

    /**
     * @var DateTimeImmutable Date and time when the inference was started.
     */
    public DateTimeImmutable $createdAt;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     * @throws Exception if the date can't be constructed.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);

        $this->inferenceId = $rawResponse["inference_id"];
        $this->modelId = $rawResponse["model_id"];
        $this->fileName = $rawResponse["file_name"];
        $this->fileAlias = $rawResponse["file_alias"];
        $this->error = new ErrorResponse($rawResponse["error"]);
        $this->createdAt = new DateTimeImmutable($rawResponse["created_at"]);
    }
}
