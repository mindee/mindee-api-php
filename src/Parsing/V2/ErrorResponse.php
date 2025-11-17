<?php

namespace Mindee\Parsing\V2;

use Mindee\Error\ErrorItem;

/**
 * Error response class.
 */
class ErrorResponse
{
    /**
     * @var integer The HTTP status code returned by the server.
     */
    public int $status;

    /**
     * @var string A human-readable explanation specific to the occurrence of the problem.
     */
    public string $detail;

    /**
     * @var string|null A short, human-readable summary of the problem.
     */
    public ?string $title;
    /**
     * @var string|null A machine-readable code specific to the occurrence of the problem.
     */
    public ?string $code;
    /**
     * @var array|mixed|null A list of explicit error details.
     */
    public ?array $errors;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->status = $serverResponse['status'];
        $this->detail = $serverResponse['detail'];
        $this->title = $serverResponse['title'] ?? null;
        $this->code = $serverResponse['code'] ?? null;
        if (isset($serverResponse['errors']) && is_array($serverResponse['errors'])) {
            $this->errors = array_map(static function ($error) {
                return new ErrorItem($error);
            }, $serverResponse['errors']);
        } else {
            $this->errors = [];
        }
    }
}
