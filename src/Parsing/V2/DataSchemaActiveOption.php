<?php

namespace Mindee\Parsing\V2;

/**
 * Data schema options activated during the inference.
 */
class DataSchemaActiveOption
{
    /**
     * @var boolean Whether the Data Schema has been replaced.
     */
    public bool $replace;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->replace = $serverResponse['replace'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Data Schema\n-----------\n:Replace: " . ($this->replace ? 'True' : 'False');
    }
}
