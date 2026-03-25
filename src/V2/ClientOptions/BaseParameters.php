<?php

namespace Mindee\V2\ClientOptions;

use Mindee\Input\PollingOptions;

/**
 * Base parameters for running an inference.
 */
abstract class BaseParameters
{
    /**
     * @var string|null Optional file alias.
     */
    public ?string $alias;

    /**
     * @var string Model ID.
     */
    public string $modelId;

    /**
     * @var array<string> Optional webhook IDs.
     */
    public array $webhooksIds;

    /**
     * @var PollingOptions Polling options.
     */
    public PollingOptions $pollingOptions;

    /**
     * @param string              $modelId        ID of the model.
     * @param string|null         $alias          Optional file alias.
     * @param array<string>|null  $webhooksIds    List of webhook IDs.
     * @param PollingOptions|null $pollingOptions Polling options.
     */
    public function __construct(string $modelId, ?string $alias, ?array $webhooksIds, ?PollingOptions $pollingOptions)
    {
        $this->modelId = $modelId;

        if (isset($alias)) {
            $this->alias = $alias;
        }
        if (isset($webhooksIds)) {
            $this->webhooksIds = $webhooksIds;
        } else {
            $this->webhooksIds = [];
        }
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }
        $this->pollingOptions = $pollingOptions;
    }

    /**
     * @return array Hash representation.
     */
    public function asHash(): array
    {
        $outHash = ['model_id' => $this->modelId];
        if (isset($this->alias)) {
            $outHash['alias'] = $this->alias;
        }

        if (isset($this->webhooksIds) && count($this->webhooksIds) > 0) {
            if (PHP_VERSION_ID < 80200 && count($this->webhooksIds) > 1) {
                // NOTE: see https://bugs.php.net/bug.php?id=51634
                error_log("PHP version is too low to support webbook array destructuring.
                \nOnly the first webhook ID will be sent to the server.");
                $outHash['webhook_ids'] = $this->webhooksIds[0];
            } else {
                foreach ($this->webhooksIds as $webhookId) {
                    $outHash['webhook_ids'][] = $webhookId;
                }
            }
        }
        return $outHash;
    }
}
