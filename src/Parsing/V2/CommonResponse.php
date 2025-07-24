<?php

namespace Mindee\Parsing\V2;

/**
 * Common response base class for V2.
 */
abstract class CommonResponse
{
    /**
     * @var array Raw HTTP response from the server.
     */
    private array $rawHttp;

    /**
     * @param array $serverResponse Raw server response array.
     */
    protected function __construct(array $serverResponse)
    {
        $this->rawHttp = $serverResponse;
    }

    /**
     * @return string Raw dump of the JSON response.
     */
    public function getRawHttp(): string
    {
        return json_encode($this->rawHttp, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
