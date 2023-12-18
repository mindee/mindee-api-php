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
     * @param array $rawResponse Raw HTTP response.
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
