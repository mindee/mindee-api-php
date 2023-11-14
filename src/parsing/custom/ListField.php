<?php

namespace Mindee\parsing\custom;

use Mindee\parsing\standard\FieldPositionMixin;

class ListFieldValueV1
{
    use FieldPositionMixin;
    public string $content;
    public float $confidence;

    public function __construct(
        array $raw_prediction
    ) {
        $this->content = $raw_prediction['content'];
        $this->confidence = $raw_prediction['confidence'];
        $this->setPosition($raw_prediction);
    }

    public function __toString(): string
    {
        return strval($this->content);
    }
}

class ListFieldV1
{
    public float $confidence;
    public bool $reconstructed;
    public ?int $pageId;
    public array $values;

    public function __construct(array $raw_prediction, bool $reconstructed = false, ?int $page_id = null)
    {
        $this->values = [];
        $this->reconstructed = $reconstructed;
        $this->confidence = 0.0;
    }

    public function contents_list(): array
    {
        $arr = [];
        foreach ($this->values as $value) {
            array_push($arr, $value->content);
        }

        return $arr;
    }

    public function contents_string(string $separator = ' ')
    {
        return implode($separator, $this->contents_list());
    }

    public function __toString(): string
    {
        return $this->contents_string();
    }
}
