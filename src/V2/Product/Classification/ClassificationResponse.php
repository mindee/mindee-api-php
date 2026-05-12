<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Classification;

use Mindee\V2\Parsing\Inference\BaseResponse;

/**
 * Classification response.
 */
class ClassificationResponse extends BaseResponse
{
    /**
     * @var ClassificationInference Inference results for the classification.
     */
    public ClassificationInference $inference;

    /**
     * @var string Slug for the inference.
     */
    public static string $slug = "classification";

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->inference = new ClassificationInference($rawResponse['inference']);
    }
}
