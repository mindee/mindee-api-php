<?php

namespace Mindee\Parsing\V2;

/**
 * Information on the Job associated to a given Inference.
 */
class InferenceJob
{
    /**
     * @var string UUID of the job.
     */
    public string $id;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->id = $serverResponse['id'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Job\n===\n"
            . ":ID: {$this->id}\n" ;
    }
}
