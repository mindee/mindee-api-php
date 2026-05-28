<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use Stringable;

/**
 * Model webhook information.
 */
class ModelWebhook implements Stringable
{
    /**
     * @var string ID of the webhook.
     */
    public string $id;
    /**
     * @var string Name of the webhook.
     */
    public string $name;
    /**
     * @var string URL of the webhook.
     */
    public string $url;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];
        $this->name = $rawResponse['name'];
        $this->url = $rawResponse['url'];
    }

    public function __toString(): string
    {
        return ":Name: $this->name\n"
            . ":ID: $this->id\n"
            . ":URL: $this->url\n";
    }
}
