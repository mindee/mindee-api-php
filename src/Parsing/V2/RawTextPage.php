<?php

namespace Mindee\Parsing\V2;

/**
 * Raw text extracted from the page.
 */
class RawTextPage
{
    /**
     * Page content as a single string.
     *
     * @var string
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
