<?php

namespace Mindee\Error;

use Mindee\Parsing\V2\ErrorResponse;

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
                    "code" => "000-000"
                ]
            )
        );
    }
}
