<?php

namespace Mindee\V2\Product\Split;

use Mindee\V2\Parsing\BaseInference;

/**
 * The inference result for a split utility request.
 */
class SplitInference extends BaseInference
{
    /**
     * @var SplitResult Result of a split utility inference.
     */
    public SplitResult $result;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->result = new SplitResult($rawResponse['result']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return parent::__toString() . "$this->result\n";
    }
}
