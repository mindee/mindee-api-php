<?php

namespace Mindee\Parsing\V2;

/**
 * Error response class.
 */
class ErrorResponse
{
    /**
     * @var integer HTTP Status code.
     */
    public int $status;

    /**
     * @var string The detail on the error.
     */
    public string $detail;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->status = $serverResponse['status'];
        $this->detail = $serverResponse['detail'];
    }
}
