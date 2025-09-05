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
     * @var InferenceActiveOptions Active options for the inference.
     */
    public InferenceActiveOptions $activeOptions;

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
        $this->activeOptions = new InferenceActiveOptions($serverResponse['active_options']);
        $this->result = new InferenceResult($serverResponse['result']);
        $this->id = $serverResponse['id'] ?? null;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Inference\n#########\n"
            . "{$this->model}\n"
            . "{$this->file}\n"
            . "{$this->activeOptions}\n"
            . "{$this->result}\n";
    }
}
