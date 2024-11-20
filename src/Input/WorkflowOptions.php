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
    public string $alias;

    /**
     * @var string|null Priority to give to the document.
     */
    public string $priority;

    /**
     * @param string|null $alias       Alias for the document.
     * @param string|null $priority    Priority for the document.
     * @param boolean     $fullTextOcr Whether to retrieve the full ocr text.
     */
    public function __construct(?string $alias = null, ?string $priority = null, bool $fullTextOcr = false)
    {
        parent::__construct($fullTextOcr);
        $this->alias = $alias;
        $this->priority = $priority;
    }
}