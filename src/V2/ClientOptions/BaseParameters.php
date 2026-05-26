<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

use function count;

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
     * @var array<string> Optional webhook IDs.
     */
    public array $webhooksIds;

    /**
     * @var string Slug of the endpoint.
     */
    public static string $slug;

    /**
     * @param string $modelId ID of the model.
     * @param string|null $alias Optional file alias.
     * @param array<string>|null $webhooksIds List of webhook IDs.
     */
    public function __construct(public string $modelId, ?string $alias, ?array $webhooksIds)
    {
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
     * @return array<string, string> Hash representation.
     */
    public function asHash(): array
    {
        $outHash = ['model_id' => $this->modelId];
        if (isset($this->alias)) {
            $outHash['alias'] = $this->alias;
        }


        if (!empty($this->webhooksIds)) {
            $outHash['webhook_ids'] = implode(',', $this->webhooksIds);
        }
        return $outHash;
    }
}
