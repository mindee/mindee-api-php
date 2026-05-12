<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

/**
 * Raw text extracted from the page.
 */
class RawTextPage
{
    /**
     * Page content as a single string.
     *
     */
    public string $content;

    /**
     * @param array $serverResponse JSON response from the server.
     */
    public function __construct(array $serverResponse)
    {
        $this->content = $serverResponse['content'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->content ?? '';
    }
}
