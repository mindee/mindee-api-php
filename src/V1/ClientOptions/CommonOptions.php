<?php

declare(strict_types=1);

namespace Mindee\V1\ClientOptions;

/**
 * Common base for regular prediction options and workflow options.
 */
abstract class CommonOptions
{
    /**
     * Prediction options.
     * @param boolean $fullText Whether to include the full Ocr text response in compatible APIs.
     *                          This performs a full Ocr operation on the server and will increase response time.
     */
    public function __construct(public bool $fullText = false) {}

    /**
     * @param boolean $fullText Whether to include the full text.
     */
    public function setFullText(bool $fullText): static
    {
        $this->fullText = $fullText;
        return $this;
    }
}
