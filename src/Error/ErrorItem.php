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
     * @param array $rawResponse Raw error response from the API.
     */
    public function __construct(array $rawResponse)
    {
        $this->detail = $rawResponse['detail'];
        $this->pointer = $rawResponse['pointer'];
    }
}
