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
    public \DateTimeImmutable $issued_at;
    /**
     * @var \DateTimeImmutable Timestamp of the request after it has been completed.
     */
    public \DateTimeImmutable $available_at;
    /**
     * @var string Status of the request, as seen by the API.
     */
    public string $status;
    /**
     * @var integer Time (ms) taken for the request to be processed by the API.
     */
    public int $millisecs_taken;

    /**
     * @param array $raw_response Raw prediction array.
     * @throws \Mindee\Error\MindeeApiException Throws if a date is faulty.
     */
    public function __construct(array $raw_response)
    {
        try {
            $this->issued_at = new \DateTimeImmutable($raw_response['issued_at']);
        } catch (\Exception $e) {
            try {
                $this->issued_at = new \DateTimeImmutable(strtotime($raw_response['issued_at']));
            } catch (\Exception $e2) {
                throw new MindeeApiException("Could not create date from " . $raw_response['issued_at']);
            }
        }
        $this->id = $raw_response['id'];
        $this->status = $raw_response['status'];
        if (array_key_exists('available_at', $raw_response)) {
            try {
                $this->available_at = new \DateTimeImmutable($raw_response['available_at']);
            } catch (\Exception $e) {
                try {
                    $this->available_at = new \DateTimeImmutable(strtotime($raw_response['available_at']));
                } catch (\Exception $e2) {
                    throw new MindeeApiException("Could not create date from " . $raw_response['issued_at']);
                }
            }
            $ts1 = (int)$this->available_at->format('Uv');
            $ts2 = (int)$this->issued_at->format('Uv');
            $this->millisecs_taken = $ts2 - $ts1;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $obj_as_json = get_object_vars($this);
        ksort($obj_as_json);

        return json_encode($obj_as_json, JSON_PRETTY_PRINT);
    }
}
