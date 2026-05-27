<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

/**
 * Common response base class for V2.
 */
abstract class BaseResponse
{
    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawHttp Raw server response array.
     */
    protected function __construct(private readonly array $rawHttp) {}

    /**
     * @return string Raw dump of the JSON response.
     */
    public function getRawHttp(): string
    {
        return json_encode($this->rawHttp, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
