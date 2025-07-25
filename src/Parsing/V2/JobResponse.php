<?php

namespace Mindee\Parsing\V2;

/**
 * Job response class.
 */
class JobResponse extends CommonResponse
{
    /**
     * @var Job Job for the polling.
     */
    public Job $job;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        parent::__construct($serverResponse);
        $this->job = new Job($serverResponse['job']);
    }
}
