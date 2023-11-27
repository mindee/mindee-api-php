<?php

namespace Mindee\Parsing\Standard;

use Mindee\Geometry\BBox;
use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;

use function Mindee\Geometry\createBoundingBoxFrom;

abstract class BaseField
{
    use FieldConfidenceMixin;

    public $value;
    public bool $reconstructed;
    public ?int $pageId;

    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'value'
    ) {
        if (!isset($page_id) && (array_key_exists('page_id', $raw_prediction) && isset($raw_prediction['page_id']))) {
            $this->pageId = $raw_prediction['page_id'];
        } else {
            $this->pageId = $page_id;
        }
        $this->reconstructed = $reconstructed;
        if (array_key_exists($value_key, $raw_prediction) && $raw_prediction[$value_key] != 'N/A') {
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
