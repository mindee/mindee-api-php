<?php

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
     * @var array|null Indexes of the page to apply the transformations to.
     */
    public ?array $pageIndexes;
    /**
     * @var string Operation to apply to the page.
     */
    public string $operation;
    /**
     * @var integer Apply the operation only if the document has at least this many pages.
     */
    public int $onMinPage;

    /**
     * @param array|null $pageIndexes Indexes of the page.
     * @param string     $operation   Operation to apply.
     * @param integer    $onMinPage   Minimum page amount.
     */
    public function __construct(
        ?array $pageIndexes = null,
        string $operation = KEEP_ONLY,
        int $onMinPage = 0
    ) {
        $this->pageIndexes = $pageIndexes;
        $this->operation = $operation;
        $this->onMinPage = $onMinPage;
    }


    /**
     * Checks whether the options are set.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        if (
            ($this->pageIndexes !== null && $this->pageIndexes !== []) ||
            $this->operation !== KEEP_ONLY ||
            $this->onMinPage !== 0
        ) {
            return false;
        }
        return true;
    }
}
