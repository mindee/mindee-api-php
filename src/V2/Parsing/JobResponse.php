<?php

namespace Mindee\V2\Parsing;

use Mindee\V2\Parsing\Inference\BaseResponse;

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
