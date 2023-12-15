<?php

namespace Mindee\Input;

use Mindee\Error\MindeeApiException;

/**
 * Handles options tied to asynchronous parsing.
 */
class EnqueueAndParseMethodOptions
{
    /**
     * @var integer Initial delay (in seconds) before attempting to poll a queue.
     */
    public int $initialDelaySec;
    /**
     * @var integer Delay (in seconds) between successive attempts to poll a queue.
     */
    public int $delaySec;
    /**
     * @var integer Maximum amount of retries for a queue.
     */
    public int $maxRetries;

    /**
     *
     */
    public function __construct()
    {
        $this->initialDelaySec = 4;
        $this->delaySec = 2;
        $this->maxRetries = 30;
    }


    /**
     * @param integer $delay Delay between polls.
     * @return $this
     * @throws \Mindee\Error\MindeeApiException Throws if the initial parsing delay is less than 4 seconds.
     */
    public function setInitialDelaySec(int $delay): EnqueueAndParseMethodOptions
    {
        if ($delay < 2) {
            throw new MindeeApiException("Cannot set initial parsing delay to less than 4 seconds.");
        }
        $this->initialDelaySec = $delay;
        return $this;
    }

    /**
     * @param integer $delay Delay between successive attempts to poll a queue.
     * @return $this
     * @throws \Mindee\Error\MindeeApiException Throws if the delay is less than 2 seconds.
     */
    public function setDelaySec(int $delay): EnqueueAndParseMethodOptions
    {
        if ($delay < 2) {
            throw new MindeeApiException("Cannot set auto-parsing delay to less than 2 seconds.");
        }
        $this->delaySec = $delay;
        return $this;
    }

    /**
     * @param integer $maxRetries Maximum allowed retries. Will default to 10 if an invalid number is provided.
     * @return $this
     */
    public function setMaxRetries(int $maxRetries): EnqueueAndParseMethodOptions
    {
        if (!$maxRetries || $maxRetries < 0) {
            $this->maxRetries = 10;
            error_log("Notice: setting the amount of retries for auto-parsing to 10");
        } else {
            $this->delaySec = $maxRetries;
        }
        return $this;
    }
}
