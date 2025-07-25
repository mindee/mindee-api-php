<?php

namespace Mindee\Input;

/**
 * Parameters accepted by the asynchronous **inference** v2 endpoint.
 */
class InferenceParameters
{
    /**
     * @var string Model ID.
     */
    public string $modelId;

    /**
     * @var boolean Whether to enable Retrieval-Augmented Generation.
     */
    public bool $rag;

    /**
     * @var string|null Optional file alias.
     */
    public ?string $alias;

    /**
     * @var array<string> Optional webhook IDs.
     */
    public array $webhooksIds;

    /**
     * @var PollingOptions Polling options.
     */
    public PollingOptions $pollingOptions;

    /**
     * @var boolean Whether to close the file after enqueuing.
     */
    public bool $closeFile;

    /**
     * @param string              $modelId        ID of the model.
     * @param boolean|null        $rag            Whether to enable Retrieval-Augmented Generation.
     * @param string|null         $alias          Optional file alias.
     * @param array<string>|null  $webhooksIds    List of webhook IDs.
     * @param PollingOptions|null $pollingOptions Polling options.
     * @param boolean|null        $closeFile      Whether to close the file after enqueuing.
     */
    public function __construct(
        string $modelId,
        ?bool $rag = null,
        ?string $alias = null,
        ?array $webhooksIds = null,
        ?PollingOptions $pollingOptions = null,
        ?bool $closeFile = null
    ) {
        $this->modelId = $modelId;
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }
        $this->pollingOptions = $pollingOptions;
        $this->rag = (bool) $rag;
        $this->closeFile = (bool) $closeFile;
        if (isset($alias)) {
            $this->alias = $alias;
        }
        if (isset($webhooksIds)) {
            $this->webhooksIds = $webhooksIds;
        } else {
            $this->webhooksIds = [];
        }
    }
}
