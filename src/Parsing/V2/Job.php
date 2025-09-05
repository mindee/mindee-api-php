<?php

namespace Mindee\Parsing\V2;

use DateTime;
use Exception;

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
     * @var DateTime|null Timestamp of the job creation.
     */
    public ?DateTime $createdAt;

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
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->id = $serverResponse['id'];

        $this->status = $serverResponse['status'];

        $this->error = null;
        if (
            !empty($serverResponse['error'])
        ) {
            $this->error = new ErrorResponse($serverResponse['error']);
        }

        $this->createdAt = isset($serverResponse['created_at'])
            ? $this->parseDate($serverResponse['created_at'])
            : null;

        $this->modelId = $serverResponse['model_id'];
        $this->pollingUrl = $serverResponse['polling_url'];
        $this->filename = $serverResponse['filename'];
        $this->resultUrl = $serverResponse['result_url'] ?? null;
        $this->alias = $serverResponse['alias'];
        if (array_key_exists("webhooks", $serverResponse)) {
            foreach ($serverResponse['webhooks'] as $webhook) {
                $this->webhooks[] = new JobWebhook($webhook);
            }
        } else {
            $this->webhooks = [];
        }
    }

    /**
     * Parse a date string into a DateTime object.
     *
     * @param string|null $dateString Date string to parse.
     * @return DateTime|null
     */
    private function parseDate(?string $dateString): ?DateTime
    {
        if ($dateString === null || $dateString === '') {
            return null;
        }

        try {
            return new DateTime($dateString);
        } catch (Exception $e) {
            return null;
        }
    }
}
