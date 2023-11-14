<?php

namespace Mindee\parsing\standard;

trait FieldPositionMixin
{
    public $polygon; // TODO: polygon & geometry module
    public $boundingBox; // TODO: bounding box
    public ?int $page_id;

    protected function setPosition(array $raw_prediction)
    {
        $this->boundingBox = null;
        $this->polygon = $raw_prediction['polygon']; // TODO: polygon etc.
    }
}

trait FieldConfidenceMixin
{
    public float $confidence;

    protected function setConfidence(array $raw_prediction)
    {
        if (array_key_exists('confidence', $raw_prediction) && $raw_prediction['confidence']) {
            $this->confidence = $raw_prediction['confidence'];
        } else {
            $this->confidence = 0.0;
        }
    }
}

abstract class BaseField
{
    use FieldConfidenceMixin;

    public $value;
    public bool $reconstructed;
    public ?int $pageId;

    public function __construct(
        array $raw_prediction,
        string $value_key = 'value',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        if (!isset($page_id) && (array_key_exists('page_id', $raw_prediction) && isset($raw_prediction['page_id']))) {
            $this->pageId = $raw_prediction['page_id'];
        } else {
            $this->pageId = $page_id;
        }
        $this->reconstructed = $reconstructed;
        if (!array_key_exists($value_key, $raw_prediction) && $raw_prediction[$value_key] != 'N/A') {
            $this->value = $raw_prediction[$value_key];
            $this->setConfidence($raw_prediction);
        } else {
            $this->value = null;
        }
    }

    public function __compare(BaseField $obj): bool
    {
        return $this->value == $obj->value;
    }

    public function __toString(): string
    {
        return isset($this->value) ? strval($this->value) : '';
    }
}
