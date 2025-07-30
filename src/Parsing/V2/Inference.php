<?php

namespace Mindee\Parsing\V2;

/**
 * Inference class.
 */
class Inference
{
    /**
     * @var InferenceModel Model info for the inference.
     */
    public InferenceModel $model;

    /**
     * @var InferenceFile File info for the inference.
     */
    public InferenceFile $file;

    /**
     * @var InferenceResult Result of the inference.
     */
    public InferenceResult $result;

    /**
     * @var string|null ID of the inference.
     */
    public ?string $id;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->model = new InferenceModel($serverResponse['model']);
        $this->file = new InferenceFile($serverResponse['file']);
        $this->result = new InferenceResult($serverResponse['result']);
        $this->id = $serverResponse['id'] ?? null;
    }

    /**
     * @return string String representation.
     */
    public function toString(): string
    {
        return "Inference\n" .
            "#########\n" .
            "Model\n" .
            "=====\n" .
            ":ID: {$this->model->id}\n\n" .
            $this->file->toString() . "\n" .
            $this->result . "\n";
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
