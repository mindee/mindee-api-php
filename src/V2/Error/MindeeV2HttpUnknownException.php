<?php

declare(strict_types=1);

namespace Mindee\V2\Error;

use Mindee\V2\Parsing\Error\ErrorResponse;

/**
 * Unknown HTTP error for the V2 API.
 */
class MindeeV2HttpUnknownException extends MindeeV2HttpException
{
    /**
     * @param string|null $response Faulty server response.
     */
    public function __construct(?string $response)
    {
        parent::__construct(
            new ErrorResponse(
                [
                    "status" => -1,
                    "detail" => "Couldn't deserialize server error. Found: $response",
                    "title" => "Unknown error",
                    "code" => "000-000",
                ]
            )
        );
    }
}
