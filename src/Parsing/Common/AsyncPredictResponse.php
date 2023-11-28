<?php

namespace Mindee\Parsing\Common;

/**
 * Async Response Wrapper class for a Predict response.
 *
 * Links a Job to a future PredictResponse.
 */
class AsyncPredictResponse extends ApiResponse
{
    /**
     * @var \Mindee\Parsing\Common\Job Job object link to the prediction.
     * As long as it isn't complete, the prediction doesn't exist.
     */
    public Job $job;
    /**
     * @var \Mindee\Parsing\Common\Document|null Document object. Can be null when enqueuing.
     */
    public ?Document $document;

    /**
     * @param string $prediction_type Type of prediction.
     * @param array  $raw_response    Raw HTTP response.
     */
    public function __construct(string $prediction_type, array $raw_response)
    {
        parent::__construct($raw_response);
        if (array_key_exists('document', $raw_response)) {
            $this->document = new Document($prediction_type, $raw_response['document']);
        }
        $this->job = new Job($raw_response['job']);
    }
}
