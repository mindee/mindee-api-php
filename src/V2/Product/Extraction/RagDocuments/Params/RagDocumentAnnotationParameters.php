<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments\Params;

use InvalidArgumentException;
use Mindee\Error\MindeeInputException;
use Mindee\V2\ClientOptions\BaseAnnotationParameters;
use Mindee\V2\Product\Extraction\RagDocuments\RagAnnotation;

use function is_array;
use function is_string;

/**
 * Annotation parameters for RAG documents.
 */
class RagDocumentAnnotationParameters extends BaseAnnotationParameters
{
    /**
     * @var RagAnnotation|null Field-level RAG annotation and guidelines configuration for the document.
     */
    public ?RagAnnotation $annotation;

    /**
     * @param string $documentId Unique identifier of the document.
     * @param string|null $status New public status to apply to the document.
     * @param string|RagAnnotation|null $annotation RAG annotation as an object or JSON string.
     * @throws MindeeInputException Throws if the annotation format is invalid.
     */
    public function __construct(
        string $documentId,
        public ?string $status = null,
        string|RagAnnotation|null $annotation = null
    ) {
        parent::__construct($documentId);

        if ($annotation instanceof RagAnnotation) {
            $this->annotation = $annotation;
        } elseif (is_string($annotation)) {
            $rawAnnotation = json_decode($annotation, true);
            if (!is_array($rawAnnotation)) {
                throw new MindeeInputException("Invalid RAG Annotation format.");
            }
            $this->annotation = new RagAnnotation($rawAnnotation);
        } else {
            $this->annotation = null;
        }
    }

    /**
     * @return array<string, mixed> Request parameters.
     * @throws InvalidArgumentException Throws if the document ID is missing.
     */
    public function getRequestParameters(): array
    {
        if (empty($this->documentId)) {
            throw new InvalidArgumentException("DocumentId is required in RagDocumentsAnnotationParameters");
        }

        $parameters = [];

        if ($this->status !== null) {
            $parameters['status'] = $this->status;
        }

        if ($this->annotation !== null) {
            $parameters['annotation'] = $this->annotation->toArray();
        }

        return $parameters;
    }
}
