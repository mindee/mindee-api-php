<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

/**
 * Information on the API request made to the server.
 */
class ApiRequest
{
    /**
     * @var array<string, mixed>|string|null Error content, if any.
     */
    public mixed $error;
    /**
     * @var array<string, mixed>|string|null Information on the target resources
     */
    public mixed $resources;
    /**
     * @var array<string,mixed>|string Status as sent back by the API.
     */
    public mixed $status;
    /**
     * @var integer HTTP status code.
     */
    public int $statusCode;
    /**
     * @var string|null URL of the request.
     */
    public ?string $url;

    /**
     * @param array<string, mixed> $rawResponse Raw HTTP response.
     */
    public function __construct(array $rawResponse)
    {
        $this->url = $rawResponse['url'];
        $this->error = $rawResponse['error'];
        $this->resources = $rawResponse['resources'];
        $this->status = $rawResponse['status'];
        $this->statusCode = $rawResponse['status_code'];
    }
}
