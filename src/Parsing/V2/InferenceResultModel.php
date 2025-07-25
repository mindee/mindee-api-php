<?php

namespace Mindee\Parsing\V2;

/**
 * Inference result model class.
 */
class InferenceResultModel
{
    /**
     * @var string ID of the model.
     */
    public string $id;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->id = $serverResponse['id'];
    }
}
