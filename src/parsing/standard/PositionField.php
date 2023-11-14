<?php

namespace Mindee\parsing\standard;

class PositionField extends BaseField
{
    public string $value; // TODO: implement geometry module before doing this
    public $polygon;
    public $quadrangle;
    public $rectangle;
    public $boundingBox;

    public function __construct(
        array $raw_prediction,
        string $value_key = 'polygon',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $value_key, $reconstructed, $page_id);
    }
}
