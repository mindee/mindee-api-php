<?php

namespace Mindee\Input;

use Mindee\Error\MindeeApiException;

class EnqueueAndParseMethodOptions
{
    public int $initialDelaySec;
    public int $delaySec;
    public int $maxRetries;

    function __construct()
    {
        $this->initialDelaySec = 6;
        $this->delaySec = 3;
        $this->maxRetries = 10;
    }


    public function setInitialDelaySec(int $delay)
    {
        if ($delay < 2) {
            throw new MindeeApiException("Cannot set initial parsing delay to less than 4 seconds.");
        }
        $this->initialDelaySec = $delay;
        return $this;
    }

    public function setDelaySec(int $delay)
    {
        if ($delay < 2) {
            throw new MindeeApiException("Cannot set auto-parsing delay to less than 2 seconds.");
        }
        $this->delaySec = $delay;
        return $this;
    }

    public function setMaxRetries(int $maxRetries)
    {
        if (!$maxRetries || $maxRetries < 0) {
            $this->maxRetries = 0;
            error_log("Notice: setting the amount of retries for auto-parsing to 0");
        } else {
            $this->delaySec = $maxRetries;
        }
        return $this;
    }
}
