<?php

namespace Mindee\Error;

/**
 * Exceptions relating to HTTP errors for the V2 API.
 */
class MindeeV2HttpException extends MindeeException
{
    /**
     * @var integer Status code as sent by the server.
     */
    public int $status;
    /**
     * @var string|null Details on the exception.
     */
    public ?string $detail;

    /**
     * @param integer     $status HTTP status code, defaults to -1 if not set.
     * @param string|null $detail Optional details on the exception.
     */
    public function __construct(int $status, string $detail = null)
    {
        parent::__construct("HTTP Error $status - $detail");
        $this->status = $status;
        $this->detail = $detail;
    }
}
