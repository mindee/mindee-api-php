<?php

namespace Mindee\Parsing\Common;

use Mindee\Parsing\Standard\BaseField;

class OrientationField extends BaseField
{
    public $value;

    public function __construct(
        array $raw_prediction,
        string $value_key = 'value',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $value_key, $reconstructed, $page_id);
        $this->value = 0;
        if (array_key_exists($value_key, $raw_prediction) && is_numeric($raw_prediction[$value_key])) {
            $this->value = intval($raw_prediction[$value_key]);
            if (!in_array($this->value, [0, 90, 180, 270])) {
                $this->value = 0;
            }
        }
    }
}
