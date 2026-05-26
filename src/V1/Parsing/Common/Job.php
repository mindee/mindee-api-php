<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

use DateTimeImmutable;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeAPIException;
use Exception;
use Stringable;

use function array_key_exists;

/**
 * Job class for asynchronous requests.
 *
 * Will hold information on the queue a document has been submitted to.
 */
class Job implements Stringable
{
    /**
     * @var string|null ID of the job sent by the API in response to an enqueue request.
     */
    public ?string $id;
    /**
     * @var DateTimeImmutable|null Timestamp of the request reception by the API.
     */
    public ?DateTimeImmutable $issuedAt;
    /**
     * @var DateTimeImmutable|null Timestamp of the request after it has been completed.
     */
    public ?DateTimeImmutable $availableAt;
    /**
     * @var string|null Status of the request, as seen by the API.
     */
    public ?string $status;
    /**
     * @var integer|null Time (ms) taken for the request to be processed by the API.
     */
    public ?int $millisecsTaken;
    /**
     * @var array<string, integer|float|string|bool|null|array<mixed>>|null Information about an error that occurred during the job processing.
     */
    public ?array $error = null;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw prediction array.
     * @throws MindeeAPIException Throws if a date is faulty.
     */
    public function __construct(array $rawResponse)
    {
        try {
            $this->issuedAt = new DateTimeImmutable($rawResponse['issued_at']);
        } catch (Exception) {
            try {
                $timestamp = strtotime($rawResponse['issued_at']);
                if ($timestamp === false) {
                    throw new Exception("Invalid date format");
                }
                $this->issuedAt = new DateTimeImmutable('@' . $timestamp);
            } catch (Exception $e) {
                throw new MindeeAPIException(
                    "Could not create date from " . $rawResponse['issued_at'],
                    ErrorCode::API_UNPROCESSABLE_ENTITY,
                    previous: $e
                );
            }
        }
        $this->id = $rawResponse['id'];
        $this->status = $rawResponse['status'];
        if (
            array_key_exists('available_at', $rawResponse)
            && $rawResponse['available_at'] !== null && strtotime($rawResponse['available_at'])
        ) {
            try {
                $this->availableAt = new DateTimeImmutable($rawResponse['available_at']);
            } catch (Exception $e) {
                try {
                    $timestamp = strtotime($rawResponse['available_at']);
                    if ($timestamp === false) {
                        throw new Exception("Invalid date format");
                    }
                    $this->availableAt = new DateTimeImmutable('@' . $timestamp);
                } catch (Exception) {
                    throw new MindeeAPIException(
                        "Could not create date from " . $rawResponse['available_at'],
                        ErrorCode::API_UNPROCESSABLE_ENTITY,
                        $e
                    );
                }
            }
            $ts1 = (int) $this->availableAt->format('Uv');
            $ts2 = (int) $this->issuedAt->format('Uv');
            $this->millisecsTaken = $ts2 - $ts1;
        } else {
            $this->availableAt = null;
            $this->millisecsTaken = null;
        }
    }

    /**
     */
    public function __toString(): string
    {
        $objAsJson = get_object_vars($this);
        ksort($objAsJson);

        return (string) json_encode($objAsJson, JSON_PRETTY_PRINT);
    }
}
