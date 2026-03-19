<?php

namespace Mindee\V2\Product\Crop;

use Mindee\V2\Parsing\BaseInference;

/**
 * The inference result for a crop utility request.
 */
class CropInference extends BaseInference
{
    /**
     * @var CropResult Result of a crop utility inference.
     */
    public CropResult $result;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->result = new CropResult($rawResponse['result']);
    }

    /**
     * @return string Raw server response array.
     */
    public function __toString(): string
    {
        return parent::__toString() . "$this->result\n";
    }
}
