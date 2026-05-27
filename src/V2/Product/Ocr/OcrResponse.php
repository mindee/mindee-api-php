<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Ocr;

use Mindee\V2\Parsing\Inference\BaseResponse;

/**
 * Response for an Ocr utility inference.
 */
class OcrResponse extends BaseResponse
{
    /**
     * @var OcrInference Result of an Ocr inference.
     */
    public OcrInference $inference;

    /**
     * @var string Slug for the inference.
     */
    public static string $slug = "ocr";

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->inference = new OcrInference($rawResponse['inference']);
    }
}
