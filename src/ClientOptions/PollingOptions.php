<?php

declare(strict_types=1);

namespace Mindee\ClientOptions;

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
     *
     * @param float $initialDelaySec Initial delay (in seconds) before attempting to poll a queue.
     * @param float $delaySec Delay (in seconds) between successive attempts to poll a queue.
     * @param integer $maxRetries Maximum number of retries for a queue.
     * @throws MindeeApiException Throws if any delay value is below the allowed minimum.
     */
    public function __construct(float $initialDelaySec = 2.0, float $delaySec = 1.5, int $maxRetries = 80)
    {
        $this->setInitialDelaySec($initialDelaySec);
        $this->setDelaySec($delaySec);
        $this->setMaxRetries($maxRetries);
    }

    /**
     * @param float $initialDelay Delay between polls.
     * @return $this
     * @throws MindeeApiException Throws if the initial parsing delay is less than the minimum.
     */
    public function setInitialDelaySec(float $initialDelay): self
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
     * @param float $delay Delay between successive attempts to poll a queue.
     * @return $this
     * @throws MindeeApiException Throws if the delay is too low.
     */
    public function setDelaySec(float $delay): self
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
    public function setMaxRetries(int $maxRetries): self
    {
        if ($maxRetries <= 0) {
            $this->maxRetries = 80;
            error_log("Notice: setting the amount of retries for auto-parsing to 80.");
        } else {
            $this->maxRetries = $maxRetries;
        }
        return $this;
    }
}
