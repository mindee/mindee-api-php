<?php

namespace Mindee\Parsing\V2;

/**
 * Inference response class for V2.
 */
class InferenceResponse extends BaseResponse
{
    /**
     * @var Inference Inference result.
     */
    public Inference $inference;

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
        $this->inference = new Inference($rawResponse['inference']);
    }
}
