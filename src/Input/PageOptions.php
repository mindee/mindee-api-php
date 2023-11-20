<?php

namespace Mindee\Input;

const KEEP_ONLY = 'KEEP_ONLY';
const REMOVE = 'REMOVE';

class PageOptions
{
    public ?array $pageIndexes;
    public string $operation;
    public int $onMinPage;

    public function __construct(
        ?array $pageIndexes = null,
        string $operation = KEEP_ONLY,
        int $onMinPage = 0)
    {
        $this->pageIndexes = $pageIndexes;
        $this->operation = $operation;
        $this->onMinPage = $onMinPage;
    }
}
