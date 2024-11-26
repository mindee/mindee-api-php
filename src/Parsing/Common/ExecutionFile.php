<?php

namespace Mindee\Parsing\Common;

/**
 * Representation of a workflow execution's file data.
 */
class ExecutionFile
{
    /**
     * @var string|null File name.
     */
    public ?string $name;

    /**
     * @var string|null Optional alias for the file
     */
    public ?string $alias;

    /**
     * @param array $rawResponse Raw HTTP response.
     */
    public function __construct(array $rawResponse)
    {
        $this->name = $rawResponse['name'] ?? null;
        $this->alias = $rawResponse['alias'] ?? null;
    }
}
