<?php

namespace Mindee\Parsing\Common;

class ApiRequest
{
    public array $error;
    public array $resources;
    public string $status;
    public int $statusCode;
    public string $url;

    public function __construct(array $raw_response)
    {
        $this->url = $raw_response['url'];
        $this->error = $raw_response['error'];
        $this->resources = $raw_response['resources'];
        $this->status = $raw_response['status'];
        $this->statusCode = $raw_response['status_code'];
    }
}
