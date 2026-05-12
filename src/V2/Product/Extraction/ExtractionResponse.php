<?php

namespace Mindee\V2\Product\Extraction;

use Mindee\V2\Parsing\Inference\BaseResponse;

/**
 * ExtractionInference response class for V2.
 */
class ExtractionResponse extends BaseResponse
{
    /**
     * @var ExtractionInference ExtractionInference result.
     */
    public ExtractionInference $inference;

    /**
     * @var string Slug for the inference.
     */
    public static string $slug = "extraction";

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->inference = new ExtractionInference($rawResponse['inference']);
    }
}
