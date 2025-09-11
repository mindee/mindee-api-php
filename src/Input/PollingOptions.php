<?php

namespace Mindee\Input;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeApiException;

const MINIMUM_INITIAL_DELAY_SECONDS = 1.0;
const MINIMUM_DELAY_SECONDS = 1.0;

/**
 * Handles options tied to asynchronous parsing.
 */
class PollingOptions
{
    /**
     * @var float Initial delay (in seconds) before attempting to poll a queue.
     */
    public float $initialDelaySec;
    /**
     * @var float Delay (in seconds) between successive attempts to poll a queue.
     */
    public float $delaySec;
    /**
     * @var integer Maximum number of retries for a queue.
     */
    public int $maxRetries;

    /**
     * Polling Options.
     */
    public function __construct()
    {
        $this->initialDelaySec = 2.0;
        $this->delaySec = 1.5;
        $this->maxRetries = 80;
    }

    /**
     * @param integer $initialDelay Delay between polls.
     * @return $this
     * @throws MindeeApiException Throws if the initial parsing delay is less than 4 seconds.
     */
    public function setInitialDelaySec(int $initialDelay): PollingOptions
    {
        if ($initialDelay < MINIMUM_INITIAL_DELAY_SECONDS) {
            throw new MindeeApiException(
                "Cannot set initial parsing delay to less than " . MINIMUM_INITIAL_DELAY_SECONDS . " second(s).",
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $this->initialDelaySec = $initialDelay;
        return $this;
    }

    /**
     * @param integer $delay Delay between successive attempts to poll a queue.
     * @return $this
     * @throws MindeeApiException Throws if the delay is too low.
     */
    public function setDelaySec(int $delay): PollingOptions
    {
        if ($delay < MINIMUM_DELAY_SECONDS) {
            throw new MindeeApiException(
                "Cannot set auto-parsing delay to less than " . MINIMUM_DELAY_SECONDS . " second(s).",
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $this->delaySec = $delay;
        return $this;
    }

    /**
     * @param integer $maxRetries Maximum allowed retries. Will default to 80 if an invalid number is provided.
     * @return $this
     */
    public function setMaxRetries(int $maxRetries): PollingOptions
    {
        if (!$maxRetries || $maxRetries < 0) {
            $this->maxRetries = 80;
            error_log("Notice: setting the amount of retries for auto-parsing to 80.");
        } else {
            $this->delaySec = $maxRetries;
        }
        return $this;
    }
}
