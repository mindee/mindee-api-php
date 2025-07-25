<?php

namespace Mindee\Parsing\V2;

/**
 * Inference class.
 */
class Inference
{
    /**
     * @var InferenceResultModel Model info for the inference.
     */
    public InferenceResultModel $model;

    /**
     * @var InferenceResultFile File info for the inference.
     */
    public InferenceResultFile $file;

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
        $this->model = new InferenceResultModel($serverResponse['model']);
        $this->file = new InferenceResultFile($serverResponse['file']);
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
