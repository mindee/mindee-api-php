<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;

/**
 * Job class for asynchronous requests.
 *
 * Will hold information on the queue a document has been submitted to.
 */
class Job
{
    /**
     * @var string ID of the job sent by the API in response to an enqueue request.
     */
    public string $id;
    /**
     * @var \DateTimeImmutable Timestamp of the request reception by the API.
     */
    public \DateTimeImmutable $issuedAt;
    /**
     * @var \DateTimeImmutable Timestamp of the request after it has been completed.
     */
    public \DateTimeImmutable $availableAt;
    /**
     * @var string Status of the request, as seen by the API.
     */
    public string $status;
    /**
     * @var integer Time (ms) taken for the request to be processed by the API.
     */
    public int $millisecsTaken;

    /**
     * @param array $rawResponse Raw prediction array.
     * @throws \Mindee\Error\MindeeApiException Throws if a date is faulty.
     */
    public function __construct(array $rawResponse)
    {
        try {
            $this->issuedAt = new \DateTimeImmutable($rawResponse['issued_at']);
        } catch (\Exception $e) {
            try {
                $this->issuedAt = new \DateTimeImmutable(strtotime($rawResponse['issued_at']));
            } catch (\Exception $e2) {
                throw new MindeeApiException("Could not create date from " . $rawResponse['issued_at']);
            }
        }
        $this->id = $rawResponse['id'];
        $this->status = $rawResponse['status'];
        if (array_key_exists('available_at', $rawResponse)) {
            try {
                $this->availableAt = new \DateTimeImmutable($rawResponse['available_at']);
            } catch (\Exception $e) {
                try {
                    $this->availableAt = new \DateTimeImmutable(strtotime($rawResponse['available_at']));
                } catch (\Exception $e2) {
                    throw new MindeeApiException("Could not create date from " . $rawResponse['issued_at']);
                }
            }
            $ts1 = (int)$this->availableAt->format('Uv');
            $ts2 = (int)$this->issuedAt->format('Uv');
            $this->millisecsTaken = $ts2 - $ts1;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $objAsJson = get_object_vars($this);
        ksort($objAsJson);

        return json_encode($objAsJson, JSON_PRETTY_PRINT);
    }
}
