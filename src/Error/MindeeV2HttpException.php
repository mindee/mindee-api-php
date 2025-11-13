<?php

namespace Mindee\Error;

use Mindee\Parsing\V2\ErrorResponse;

/**
 * Exceptions relating to HTTP errors for the V2 API.
 */
class MindeeV2HttpException extends MindeeException
{
    /**
     * @var integer Status code as sent by the server.
     */
    public int $status;
    /**
     * @var string|null Details on the exception.
     */
    public ?string $detail;
    /**
     * @var string|null Title of the error.
     */
    public ?string $title;
    /**
     * @var string|null Error code.
     * Note: PHP's `RuntimeException` class uses `$code` for the error code.
     */
    public ?string $errorCode;
    /**
     * @var array List of associated errors.
     */
    public array $errors;

    /**
     * @param ErrorResponse $response Server Error response.
     */
    public function __construct(ErrorResponse $response)
    {
        parent::__construct("HTTP $response->status - $response->title :: $response->code - $response->detail");
        $this->status = $response->status;
        $this->detail = $response->detail;
        $this->errorCode = $response->code;
        $this->title = $response->title;
        $this->errors = $response->errors;
    }
}
