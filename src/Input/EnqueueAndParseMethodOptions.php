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
     * @param integer $initialDelay Delay between polls.
     * @return $this
     * @throws MindeeApiException Throws if the initial parsing delay is less than 4 seconds.
     */
    public function setInitialDelaySec(int $initialDelay): EnqueueAndParseMethodOptions
    {
        if ($initialDelay < 4) {
            throw new MindeeApiException("Cannot set initial parsing delay to less than 4 seconds.");
        }
        $this->initialDelaySec = $initialDelay;
        return $this;
    }

    /**
     * @param integer $delay Delay between successive attempts to poll a queue.
     * @return $this
     * @throws MindeeApiException Throws if the delay is less than 2 seconds.
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
     * @param integer $maxRetries Maximum allowed retries. Will default to 30 if an invalid number is provided.
     * @return $this
     */
    public function setMaxRetries(int $maxRetries): EnqueueAndParseMethodOptions
    {
        if (!$maxRetries || $maxRetries < 0) {
            $this->maxRetries = 30;
            error_log("Notice: setting the amount of retries for auto-parsing to 30");
        } else {
            $this->delaySec = $maxRetries;
        }
        return $this;
    }
}
