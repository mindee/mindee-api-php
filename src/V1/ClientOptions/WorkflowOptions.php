<?php

declare(strict_types=1);

namespace Mindee\V1\ClientOptions;

/**
 * Handles options tied to Workflows.
 */
class WorkflowOptions extends CommonOptions
{
    /**
     * @param string|null $alias Alias for the document.
     * @param string|null $priority Priority for the document.
     * @param boolean $fullTextOcr Whether to retrieve the full ocr text.
     * @param string|null $publicUrl Priority for the document.
     * @param boolean $rag Whether to enable Retrieval-Augmented Generation.
     */
    public function __construct(
        public ?string $alias = null,
        public ?string $priority = null,
        bool $fullTextOcr = false,
        public ?string $publicUrl = null,
        public ?bool $rag = false
    ) {
        parent::__construct($fullTextOcr);
    }
}
