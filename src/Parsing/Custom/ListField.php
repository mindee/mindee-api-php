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
     * @param array        $rawPrediction Raw prediction array.
     * @param boolean      $reconstructed Whether the field has been reconstructed.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, bool $reconstructed = false, ?int $pageId = null)
    {
        $this->values = [];
        $this->reconstructed = $reconstructed;

        if (array_key_exists("values", $rawPrediction)) {
            foreach ($rawPrediction['values'] as $value) {
                if (array_key_exists("page_id", $value)) {
                    $pageId = $value["page_id"];
                }
                $this->values[] = new ListFieldValue($value, $pageId);
            }
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
