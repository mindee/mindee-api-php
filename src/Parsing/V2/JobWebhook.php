<?php

namespace Mindee\Parsing\V2;

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
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->id = $serverResponse['id'];
        $this->createdAt = isset($serverResponse['created_at'])
            ? $this->parseDate($serverResponse['created_at'])
            : null;
        $this->status = $serverResponse['status'];
        $this->error = isset($serverResponse['error'])
            ? new ErrorResponse($serverResponse['error'])
            : null;
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
