<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing;

use DateTime;
use Exception;

use function array_key_exists;

/**
 * Job information for a V2 polling attempt.
 */
class Job
{
    /**
     * @var string Job ID.
     */
    public string $id;

    /**
     * @var ErrorResponse|null Error response if any.
     */
    public ?ErrorResponse $error;

    /**
     * @var DateTime Date and time of the Job creation.
     */
    public DateTime $createdAt;

    /**
     * @var DateTime|null Date and time of the Job completion. Filled once processing is finished.
     */
    public ?DateTime $completedAt;

    /**
     * @var string ID of the model.
     */
    public string $modelId;

    /**
     * @var string Name for the file.
     */
    public string $filename;

    /**
     * @var string|null Optional alias for the file.
     */
    public ?string $alias;

    /**
     * @var string Status of the job.
     */
    public string $status;

    /**
     * @var string URL to poll for the job status.
     */
    public string $pollingUrl;

    /**
     * @var string|null URL to poll for the job result, redirects to the result if available.
     */
    public ?string $resultUrl;

    /**
     * @var JobWebhook[] ID of webhooks associated with the job.
     */
    public array $webhooks;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];

        $this->status = $rawResponse['status'];

        $this->error = null;
        if (
            !empty($rawResponse['error'])
        ) {
            $this->error = new ErrorResponse($rawResponse['error']);
        }

        $this->createdAt = $this->parseDate($rawResponse['created_at']);
        $this->completedAt = isset($rawResponse['completed_at'])
            ? $this->parseDate($rawResponse['completed_at'])
            : null;

        $this->modelId = $rawResponse['model_id'];
        $this->pollingUrl = $rawResponse['polling_url'];
        $this->filename = $rawResponse['filename'];
        $this->resultUrl = $rawResponse['result_url'] ?? null;
        $this->alias = $rawResponse['alias'];
        $this->webhooks = [];
        if (array_key_exists("webhooks", $rawResponse)) {
            foreach ($rawResponse['webhooks'] as $webhook) {
                $this->webhooks[] = new JobWebhook($webhook);
            }
        }
    }

    /**
     * Parse a date string into a DateTime object.
     *
     * @param string|null $dateString Date string to parse.
     */
    private function parseDate(?string $dateString): ?DateTime
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return new DateTime($dateString);
        } catch (Exception $e) {
            return null;
        }
    }
}
