<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\Params;

use Stringable;

/**
 * Data schema options activated during the inference.
 */
class DataSchemaActiveOption implements Stringable
{
    /**
     * @var boolean Whether the Data Schema has been replaced.
     */
    public bool $replace;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->replace = $rawResponse['replace'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Data Schema\n-----------\n:Replace: " . ($this->replace ? 'True' : 'False');
    }
}
