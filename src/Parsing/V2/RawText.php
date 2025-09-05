<?php

namespace Mindee\Parsing\V2;

/**
 * Raw text as found in the document.
 */
class RawText
{
    /**
     * @var RawTextPage[] list of pages found in the document.
     */
    public array $pages;

    /**
     * @param array $serverResponse JSON response from the server.
     */
    public function __construct(array $serverResponse)
    {
        if (array_key_exists('pages', $serverResponse)) {
            foreach ($serverResponse['pages'] as $page) {
                $this->pages[] = new RawTextPage($page);
            }
        } else {
            $this->pages = [];
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        if (empty($this->pages)) {
            return '';
        }
        return implode("\n\n", $this->pages);
    }
}
