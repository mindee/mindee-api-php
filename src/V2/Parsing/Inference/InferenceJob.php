<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

use Stringable;

/**
 * Information on the Job associated to a given Inference.
 */
class InferenceJob implements Stringable
{
    /**
     * @var string UUID of the job.
     */
    public string $id;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Job\n===\n"
            . ":ID: $this->id" ;
    }
}
