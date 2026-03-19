<?php

namespace Mindee\V2\Parsing;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\V2\InferenceFile;
use Mindee\Parsing\V2\InferenceJob;
use Mindee\Parsing\V2\InferenceModel;

/**
 * Base for all inference-based V2 products.
 */
abstract class BaseInference
{
    /**
     * @var string ID of the inference.
     */
    public string $id;

    /**
     * @var InferenceModel Model used for inference.
     */
    public InferenceModel $model;

    /**
     * @var InferenceFile File used for the inference.
     */
    public InferenceFile $file;

    /**
     * @var InferenceJob Job the inference belongs to.
     */
    public InferenceJob $job;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];
        $this->model = new InferenceModel($rawResponse['model']);
        $this->file = new InferenceFile($rawResponse['file']);
        $this->job = new InferenceJob($rawResponse['job']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $str = "Inference\n#########\n$this->job\n\n$this->model\n\n$this->file\n\n";

        return SummaryHelper::cleanOutString($str);
    }
}
