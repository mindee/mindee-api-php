<?php

namespace Mindee\Input;

/**
 * Handles options tied to Workflows.
 */
class WorkflowOptions extends CommonOptions
{
    /**
     * @var string|null Alias to give to the document.
     */
    public ?string $alias;

    /**
     * @var string|null Priority to give to the document.
     */
    public ?string $priority;

    /**
     * @var string|null A unique, encrypted URL for accessing the document validation interface without requiring
     * authentication.
     */
    public ?string $publicUrl;

    /**
     * @var boolean|null Whether to enable Retrieval-Augmented Generation.
     */
    public ?bool $rag;

    /**
     * @param string|null $alias       Alias for the document.
     * @param string|null $priority    Priority for the document.
     * @param boolean     $fullTextOcr Whether to retrieve the full ocr text.
     * @param string|null $publicUrl   Priority for the document.
     * @param boolean     $rag         Whether to enable Retrieval-Augmented Generation.
     */
    public function __construct(
        ?string $alias = null,
        ?string $priority = null,
        bool $fullTextOcr = false,
        ?string $publicUrl = null,
        bool $rag = false
    ) {
        parent::__construct($fullTextOcr);
        $this->alias = $alias;
        $this->priority = $priority;
        $this->publicUrl = $publicUrl;
        $this->rag = $rag;
    }
}
