<?php

declare(strict_types=1);

/**
 * Page options & related constants.
 */

namespace Mindee\Input;

/**
 * Only keep the selected pages.
 */
const KEEP_ONLY = 'KEEP_ONLY';
/**
 * Remove the selected pages.
 */
const REMOVE = 'REMOVE';

/**
 * Options for page handling (PDF only).
 */
class PageOptions
{
    /**
     * @param array<integer>|null $pageIndexes Indexes of the page.
     * @param string $operation Operation to apply.
     * @param integer $onMinPage Minimum page amount.
     */
    public function __construct(public ?array $pageIndexes = null, public string $operation = KEEP_ONLY, public int $onMinPage = 0) {}


    /**
     * Checks whether the options are set.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        if (
            ($this->pageIndexes !== null && $this->pageIndexes !== [])
            || $this->operation !== KEEP_ONLY
            || $this->onMinPage !== 0
        ) {
            return false;
        }
        return true;
    }
}
