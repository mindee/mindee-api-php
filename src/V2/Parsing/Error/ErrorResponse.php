<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Error;

use function is_array;

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
     * @var array<ErrorItem>|null A list of explicit error details.
     */
    public ?array $errors;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->status = $rawResponse['status'];
        $this->detail = $rawResponse['detail'];
        $this->title = $rawResponse['title'] ?? null;
        $this->code = $rawResponse['code'] ?? null;
        if (isset($rawResponse['errors']) && is_array($rawResponse['errors'])) {
            $this->errors = array_map(static fn($error) => new ErrorItem($error), $rawResponse['errors']);
        } else {
            $this->errors = [];
        }
    }
}
