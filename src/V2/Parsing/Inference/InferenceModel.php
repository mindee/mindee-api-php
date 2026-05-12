<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

/**
 * Inference result model class.
 */
class InferenceModel
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

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Model\n=====\n"
            . ":ID: $this->id" ;
    }
}
