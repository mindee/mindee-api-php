<?php

declare(strict_types=1);

namespace Mindee\V2\Product\OCR;

use Mindee\V2\Parsing\Inference\BaseResponse;

/**
 * Response for an OCR utility inference.
 */
class OCRResponse extends BaseResponse
{
    /**
     * @var OCRInference Result of an OCR inference.
     */
    public OCRInference $inference;

    /**
     * @var string Slug for the inference.
     */
    public static string $slug = "ocr";

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->inference = new OCRInference($rawResponse['inference']);
    }
}
