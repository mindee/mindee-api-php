<?php

declare(strict_types=1);

namespace Mindee\V1\ClientOptions;

/**
 * Common base for regular prediction options and workflow options.
 */
abstract class CommonOptions
{
    /**
     * @var boolean Whether to include the full OCR text response in compatible APIs.
     *              This performs a full OCR operation on the server and will increase response time.
     */
    public bool $fullText;

    /**
     * Prediction options.
     * @param boolean $fullText Whether to include the full OCR text response in compatible APIs.
     *                          This performs a full OCR operation on the server and will increase response time.
     */
    public function __construct(bool $fullText = false)
    {
        $this->fullText = $fullText;
    }

    /**
     * @param boolean $fullText Whether to include the full text.
     */
    public function setFullText(bool $fullText): static
    {
        $this->fullText = $fullText;
        return $this;
    }
}
