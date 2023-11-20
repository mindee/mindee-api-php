<?php

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Custom\ListField;

class CustomV1Page extends Prediction
{
    public array $fields;

    function __construct(array $raw_prediction)
    {
        $this->fields = [];
        foreach ($raw_prediction as $field_name => $field_contents) {
            $this->fields[$field_name] = new ListField($field_contents);
        }
    }

    public function __toString(): string
    {
        $out_str = "";
        foreach ($this->fields as $field_name => $field_value) {
            $out_str .= ":$field_name: $field_value\n";
        }
        return trim($out_str);
    }
}
