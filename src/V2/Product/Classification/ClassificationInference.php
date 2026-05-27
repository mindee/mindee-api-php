<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Classification;

use Mindee\Parsing\SummaryHelper;
use Mindee\V2\Parsing\Inference\BaseInference;

/**
 * Classification inference result.
 */
class ClassificationInference extends BaseInference
{
    /**
     * @var ClassificationResult Result of the inference.
     */
    public ClassificationResult $result;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->result = new ClassificationResult($rawResponse['result']);
    }


    /**
     * @return string String representation.
     */
    /**
     * A prettier representation.
     */
    public function __toString(): string
    {
        return SummaryHelper::cleanOutString(parent::__toString() . "$this->result\n");
    }
}
