<?php

namespace Mindee\Parsing\Custom;

/**
 * A list of value or words.
 */
class ListField
{
    /**
     * @var float Confidence score.
     */
    public float $confidence;
    /**
     * @var boolean Whether the field was reconstructed from other fields.
     */
    public bool $reconstructed;
    /**
     * @var array List of word values.
     */
    public array $values;

    /**
     * @param array   $rawPrediction Raw prediction array.
     * @param boolean $reconstructed Whether the field has been reconstructed.
     */
    public function __construct(array $rawPrediction, bool $reconstructed = false)
    {
        $this->values = [];
        $this->reconstructed = $reconstructed;

        foreach ($rawPrediction['value'] as $value) {
            $this->values[] = new ListFieldValue($value);
        }
        $this->confidence = 0.0;
    }

    /**
     * Returns the contents of the list as an array of values.
     *
     * @return array Contents as an array.
     */
    public function contentsList(): array
    {
        $arr = [];
        foreach ($this->values as $value) {
            $arr[] = $value->content;
        }

        return $arr;
    }

    /**
     * Returns the contents of a list as a concatenated string.
     *
     * @param string $separator Separator to repeat and insert between lines.
     * @return string
     */
    public function contentsString(string $separator = ' '): string
    {
        return implode($separator, $this->contentsList());
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->contentsString();
    }
}
