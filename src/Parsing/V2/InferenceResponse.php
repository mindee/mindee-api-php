<?php

namespace Mindee\Parsing\V2;

/**
 * Inference response class for V2.
 */
class InferenceResponse extends CommonResponse
{
    /**
     * @var Inference Inference result.
     */
    public Inference $inference;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        parent::__construct($serverResponse);
        $this->inference = new Inference($serverResponse['inference']);
    }
}
