<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

/**
 * Base parameters for running an inference.
 */
abstract class BaseProductParameters
{
    /**
     * @var string|null Optional file alias.
     */
    public ?string $alias;

    /**
     * @var array<string> Optional webhook IDs.
     */
    public array $webhookIds;

    /**
     * @var string Slug of the endpoint.
     */
    public static string $slug;

    /**
     * @param string $modelId ID of the model.
     * @param string|null $alias Optional file alias.
     * @param array<string>|null $webhookIds List of webhook IDs.
     */
    public function __construct(public string $modelId, ?string $alias, ?array $webhookIds)
    {
        if (isset($alias)) {
            $this->alias = $alias;
        }
        if (isset($webhookIds)) {
            $this->webhookIds = $webhookIds;
        } else {
            $this->webhookIds = [];
        }
    }

    /**
     * @return array<string, string> Hash representation.
     */
    public function asHash(): array
    {
        $outHash = ['model_id' => $this->modelId];
        if (isset($this->alias)) {
            $outHash['alias'] = $this->alias;
        }


        if (!empty($this->webhookIds)) {
            $outHash['webhook_ids'] = implode(',', $this->webhookIds);
        }
        return $outHash;
    }
}
