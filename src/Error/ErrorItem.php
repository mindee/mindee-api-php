<?php

namespace Mindee\Error;

/**
 * Explicit details on a problem.
 */
class ErrorItem
{
    /**
     * @var string|null A JSON Pointer to the location of the body property.
     */
    public ?string $pointer;
    /**
     * @var string Explicit information on the issue.
     */
    public string $detail;

    /**
     * @param string      $detail  Explicit information on the issue.
     * @param string|null $pointer A JSON Pointer to the location of the body property.
     */
    public function __construct(string $detail, ?string $pointer = null)
    {
        $this->detail = $detail;
        $this->pointer = $pointer;
    }
}
