<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

/**
 * Class for keeping track of a product's info.
 */
class Product
{
    /**
     * @var string Product's name.
     */
    public string $name;
    /**
     * @var string Product's versions.
     */
    public string $version;

    /**
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->name = $rawPrediction['name'];
        $this->version = $rawPrediction['version'];
    }

    /**
     * @return string String representation.
     */
    public function __toString()
    {
        return "$this->name v$this->version";
    }
}
