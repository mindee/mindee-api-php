<?php

namespace Mindee\Parsing\Custom;

class ListField
{
    public float $confidence;
    public bool $reconstructed;
    public array $values;

    public function __construct(array $raw_prediction, bool $reconstructed = false)
    {
        $this->values = [];
        $this->reconstructed = $reconstructed;

        foreach ($raw_prediction['value'] as $value) {
            $this->values[] = new ListFieldValue($value);
        }
        $this->confidence = 0.0;
    }

    public function contents_list(): array
    {
        $arr = [];
        foreach ($this->values as $value) {
            $arr[] = $value->content;
        }

        return $arr;
    }

    public function contents_string(string $separator = ' '): string
    {
        return implode($separator, $this->contents_list());
    }

    public function __toString(): string
    {
        return $this->contents_string();
    }
}
