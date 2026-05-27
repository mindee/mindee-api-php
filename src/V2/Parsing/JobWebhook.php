<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing;

use DateTime;
use Exception;

/**
 * JobWebhook information.
 */
class JobWebhook
{
    /**
     * @var string JobWebhook ID.
     */
    public string $id;

    /**
     * @var DateTime|null Created at date.
     */
    public ?DateTime $createdAt;

    /**
     * @var string Status of the webhook.
     */
    public string $status;

    /**
     * @var ErrorResponse|null Error response, if any.
     */
    public ?ErrorResponse $error;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];
        $this->createdAt = isset($rawResponse['created_at'])
            ? $this->parseDate($rawResponse['created_at'])
            : null;
        $this->status = $rawResponse['status'];
        $this->error = isset($rawResponse['error'])
            ? new ErrorResponse($rawResponse['error'])
            : null;
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
        } catch (Exception) {
            return null;
        }
    }
}
