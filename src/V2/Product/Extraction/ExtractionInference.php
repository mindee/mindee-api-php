<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction;

use Mindee\Parsing\SummaryHelper;
use Mindee\V2\Parsing\Inference\BaseInference;
use Mindee\V2\Parsing\Inference\InferenceActiveOptions;

/**
 * ExtractionInference class.
 */
class ExtractionInference extends BaseInference
{
    /**
     * @var InferenceActiveOptions Active options for the inference.
     */
    public InferenceActiveOptions $activeOptions;

    /**
     * @var ExtractionResult Result of the inference.
     */
    public ExtractionResult $result;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->activeOptions = new InferenceActiveOptions($rawResponse['active_options']);
        $this->result = new ExtractionResult($rawResponse['result']);
    }

    /**
     * @return string String representation.
     */
    /**
     * A prettier representation.
     */
    public function __toString(): string
    {
        $str = parent::__toString() . "$this->activeOptions\n\n$this->result\n";

        return SummaryHelper::cleanOutString($str);
    }
}
