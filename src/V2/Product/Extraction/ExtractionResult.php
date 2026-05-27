<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction;

use Mindee\V2\Parsing\Inference\Field\InferenceFields;
use Mindee\V2\Parsing\Inference\RagMetadata;
use Mindee\V2\Parsing\Inference\RawText;
use Stringable;

/**
 * Inference result class.
 */
class ExtractionResult implements Stringable
{
    /**
     * @var InferenceFields Fields contained in the inference.
     */
    public InferenceFields $fields;

    /**
     * @var RawText|null Potential options retrieved alongside the inference.
     */
    public ?RawText $rawText;

    /**
     * @var RagMetadata|null RAG metadata.
     */
    public ?RagMetadata $rag;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->fields = new InferenceFields($rawResponse['fields']);
        $this->rawText = isset($rawResponse['raw_text'])
            ? new RawText($rawResponse['raw_text'])
            : null;
        $this->rag = isset(
            $rawResponse['rag']
        ) ? new RagMetadata($rawResponse['rag']) : null;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $parts = [
            "Fields",
            "======",
            $this->fields->toString(),
        ];

        return implode("\n", $parts);
    }
}
