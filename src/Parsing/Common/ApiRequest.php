<?php

namespace Mindee\Parsing\Common;

/**
 * Information on the API request made to the server.
 */
class ApiRequest
{
    /**
     * @var array|mixed Error content, if any.
     */
    public array $error;
    /**
     * @var array|mixed Information on the target resources
     */
    public array $resources;
    /**
     * @var string|mixed Status as sent back by the API.
     */
    public string $status;
    /**
     * @var integer|mixed HTTP status code.
     */
    public int $statusCode;
    /**
     * @var string|mixed
     */
    public string $url;

    /**
     * @param array $raw_response Raw HTTP response.
     */
    public function __construct(array $raw_response)
    {
        $this->url = $raw_response['url'];
        $this->error = $raw_response['error'];
        $this->resources = $raw_response['resources'];
        $this->status = $raw_response['status'];
        $this->statusCode = $raw_response['status_code'];
    }
}
