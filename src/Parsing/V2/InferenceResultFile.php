<?php

namespace Mindee\Parsing\V2;

/**
 * Inference result file class.
 */
class InferenceResultFile
{
    /**
     * @var string Name of the file.
     */
    public string $name;

    /**
     * @var string|null Optional alias for the file.
     */
    public ?string $alias;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->name = $serverResponse['name'];
        $this->alias = $serverResponse['alias'];
    }

    /**
     * @return string String representation.
     */
    public function toString(): string
    {
        return "File\n" .
            "====\n" .
            ":Name: $this->name\n" .
            ":Alias:" . ($this->alias ? " $this->alias" : "") . "\n";
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
