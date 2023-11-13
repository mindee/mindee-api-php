<?php

namespace Mindee\parsing\common;

class Job
{
    /**
     * ID of the job sent by the API in response to an enqueue request.
     */
    public int $id;
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
        $this->issued_at = new \DateTimeImmutable(strtotime($raw_response['issued_at']));
        $this->id = $raw_response['id'];
        $this->status = $raw_response['status'];
        if (array_key_exists('available_at', $raw_response)) {
            $this->available_at = new \DateTimeImmutable(strtotime($raw_response['available_at']));
            $ts1 = (int) $this->available_at->format('Uv');
            $ts2 = (int) $this->issued_at->format('Uv');
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

class AsyncPredictReponse extends ApiResponse
{
    public Job $job;
    public ?Document $document;

    public function __construct(Inference $prediction_type, array $raw_response)
    {
        parent::__construct($raw_response);
        if (array_key_exists('document', $raw_response)) {
            $this->document = new Document($prediction_type, $raw_response['document']);
        }
        $this->job = new Job($raw_response['job']);
    }
}
