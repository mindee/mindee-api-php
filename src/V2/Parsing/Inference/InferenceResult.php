<?php

namespace Mindee\V2\Parsing\Inference;

use Mindee\V2\Parsing\Inference\Field\InferenceFields;

/**
 * Inference result class.
 */
class InferenceResult
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
     * @var RAGMetadata|null RAG metadata.
     */
    public ?RAGMetadata $rag;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->fields = new InferenceFields($serverResponse['fields']);
        $this->rawText = isset($serverResponse['raw_text'])
            ? new RawText($serverResponse['raw_text'])
            : null;
        $this->rag = isset(
            $serverResponse['rag']
        ) ? new RAGMetadata($serverResponse['rag']) : null;
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
