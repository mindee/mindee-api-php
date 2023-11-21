<?php

namespace Mindee\Parsing\Common;

class AsyncPredictResponse extends ApiResponse
{
    public Job $job;
    public ?Document $document;

    public function __construct(string $prediction_type, array $raw_response)
    {
        parent::__construct($raw_response);
        if (array_key_exists('document', $raw_response)) {
            $this->document = new Document($prediction_type, $raw_response['document']);
        }
        $this->job = new Job($raw_response['job']);
    }
}
