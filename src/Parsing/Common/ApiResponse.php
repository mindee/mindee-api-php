<?php

namespace Mindee\Parsing\Common;

abstract class ApiResponse
{
    public ApiRequest $apiRequest;
    private array $rawHttp;

    public function __construct($raw_response)
    {
        $this->apiRequest = new ApiRequest($raw_response['api_request']);
        $this->rawHttp = $raw_response;
    }

    public function getRawHttp(): string
    {
        return json_encode($this->rawHttp, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
