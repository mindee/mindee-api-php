<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

use Stringable;

use function array_key_exists;

/**
 * Raw text as found in the document.
 */
class RawText implements Stringable
{
    /**
     * @var RawTextPage[] list of pages found in the document.
     */
    public array $pages;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse JSON response from the server.
     */
    public function __construct(array $rawResponse)
    {
        if (array_key_exists('pages', $rawResponse)) {
            foreach ($rawResponse['pages'] as $page) {
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
