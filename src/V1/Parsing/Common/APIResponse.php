<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

/**
 * Base class for API responses.
 */
abstract class APIResponse
{
    /**
     * @var APIRequest Request part of the response.
     */
    public APIRequest $apiRequest;
    /**
     * @var array<string, int|float|string|bool|null|array<array-key, mixed>> Raw http result. Used for debugging purposes.
     */
    private readonly array $rawHttp;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw prediction array.
     */
    public function __construct(array $rawResponse)
    {
        $this->apiRequest = new APIRequest($rawResponse['api_request']);
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
