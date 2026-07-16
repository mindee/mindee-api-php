<?php

declare(strict_types=1);

namespace Mindee\Http;

/**
 * Custom Mindee HTTP cancellation token for polling.
 */
class CancellationToken
{
    private bool $isCanceled = false;

    /**
     * Flags the token as canceled.
     */
    public function cancel(): void
    {
        $this->isCanceled = true;
    }

    /**
     * Checks whether the token is canceled.
     * @return boolean whether the token is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->isCanceled;
    }

    /**
     * Checks whether the token is canceled, but in British.
     * @return boolean whether the token is canceled.
     */
    public function isCancelled(): bool
    {
        return $this->isCanceled;
    }
}
