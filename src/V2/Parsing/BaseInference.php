<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing;

use Mindee\Parsing\SummaryHelper;
use Mindee\V2\Parsing\Inference\InferenceFile;
use Mindee\V2\Parsing\Inference\InferenceJob;
use Mindee\V2\Parsing\Inference\InferenceModel;
use Stringable;

/**
 * Base for all inference-based V2 products.
 */
abstract class BaseInference implements Stringable
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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
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
