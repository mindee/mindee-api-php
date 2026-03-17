<?php

namespace Mindee\V2\Product\Split;

use Mindee\Parsing\V2\BaseResponse;

/**
 * Represent a split response from Mindee V2 API.
 */
class SplitResponse extends BaseResponse
{
    /**
     * @var SplitInference Contents of the inference.
     */
    public SplitInference $inference;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->inference = new SplitInference($rawResponse['inference']);
    }
}
