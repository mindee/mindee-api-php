<?php

namespace Mindee\Parsing\Common;

/**
 * Base class for API responses.
 */
abstract class ApiResponse
{
    /**
     * @var \Mindee\Parsing\Common\ApiRequest Request part of the response.
     */
    public ApiRequest $apiRequest;
    /**
     * @var array Raw http result. Used for debugging purposes.
     */
    private array $rawHttp;

    /**
     * @param array $rawResponse Raw prediction array.
     */
    public function __construct(array $rawResponse)
    {
        $this->apiRequest = new ApiRequest($rawResponse['api_request']);
        $this->rawHttp = $rawResponse;
    }

    /**
     * @return string String representation.
     */
    public function getRawHttp(): string
    {
        return json_encode($this->rawHttp, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
