<?php

namespace Mindee\Parsing\V2;

/**
 * Job response class.
 */
class JobResponse extends BaseResponse
{
    /**
     * @var Job Job for the polling.
     */
    public Job $job;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->job = new Job($rawResponse['job']);
    }
}
