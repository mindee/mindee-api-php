<?php

namespace Mindee\Parsing\V2;

/**
 * Raw text class.
 */
class RawText
{
    /**
     * @var integer The page number the text was found on.
     */
    public int $page;

    /**
     * The text content found on the page.
     *
     * @var string
     */
    public string $content;

    /**
     * @param array $serverResponse JSON response from the server.
     */
    public function __construct(array $serverResponse)
    {
        $this->page = $serverResponse['page'];
        $this->content = $serverResponse['content'];
    }

    /**
     * @return string String representation.
     */
    public function toString(): string
    {
        return "Page $this->page: $this->content";
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
