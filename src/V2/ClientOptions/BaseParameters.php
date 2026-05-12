<?php

namespace Mindee\V2\ClientOptions;

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
     * @param string             $modelId     ID of the model.
     * @param string|null        $alias       Optional file alias.
     * @param array<string>|null $webhooksIds List of webhook IDs.
     */
    public function __construct(string $modelId, ?string $alias, ?array $webhooksIds)
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
            $outHash['webhook_ids'] = implode(',', $this->webhooksIds);
        }
        return $outHash;
    }
}
