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
     * @param string $predictionType Type of prediction.
     * @param array  $rawResponse    Raw HTTP response.
     */
    public function __construct(string $predictionType, array $rawResponse)
    {
        parent::__construct($rawResponse);
        if (array_key_exists('document', $rawResponse)) {
            $this->document = new Document($predictionType, $rawResponse['document']);
        }
        $this->job = new Job($rawResponse['job']);
    }
}
