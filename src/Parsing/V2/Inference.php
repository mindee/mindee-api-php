<?php

namespace Mindee\Parsing\V2;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\V2\Parsing\BaseInference;

/**
 * Inference class.
 */
class Inference extends BaseInference
{
    /**
     * @var InferenceActiveOptions Active options for the inference.
     */
    public InferenceActiveOptions $activeOptions;

    /**
     * @var InferenceResult Result of the inference.
     */
    public InferenceResult $result;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->activeOptions = new InferenceActiveOptions($rawResponse['active_options']);
        $this->result = new InferenceResult($rawResponse['result']);
    }

    /**
     * @return string String representation.
     */
    /**
     * A prettier representation.
     * @return string
     */
    public function __toString(): string
    {
        $str = parent::__toString() . "$this->activeOptions\n\n$this->result\n";

        return SummaryHelper::cleanOutString($str);
    }
}
