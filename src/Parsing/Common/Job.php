<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;

class Job
{
    /**
     * ID of the job sent by the API in response to an enqueue request.
     */
    public string $id;
    /**
     * Timestamp of the request reception by the API.
     */
    public \DateTimeImmutable $issued_at;
    /**
     * Timestamp of the request after it has been completed.
     */
    public \DateTimeImmutable $available_at;
    /**
     * Status of the request, as seen by the API.
     */
    public string $status;
    /**
     * Time (ms) taken for the request to be processed by the API.
     */
    public int $millisecs_taken;

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

    public function __toString(): string
    {
        $obj_as_json = get_object_vars($this);
        ksort($obj_as_json);

        return json_encode($obj_as_json, JSON_PRETTY_PRINT);
    }
}
