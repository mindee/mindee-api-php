<?php

namespace Mindee\product\custom;

use Mindee\parsing\common\Prediction;
use Mindee\parsing\custom\ClassificationField;
use Mindee\parsing\custom\CustomLine;
use Mindee\parsing\custom\ListField;

class CustomV1Document extends Prediction
{
    public array $fields;
    public array $classifications;

    public function __construct(array $raw_prediction)
    {
        $this->fields = [];
        $this->classifications = [];

        foreach ($raw_prediction as $field_name => $field_contents) {
            if (array_key_exists("value", $field_contents)) {
                $this->classifications[$field_name] = new ClassificationField($field_contents);
            } elseif (array_key_exists("values", $field_contents)) {
                $this->fields[$field_name] = new ListField($field_contents);
            }
        }
    }

    public function columnsToLineItems(array $anchor_names, array $field_names, float $height_tolerance): array
    {
        return CustomLine::getLineItems(
            $anchor_names,
            $field_names,
            $this->fields,
            $height_tolerance
        );
    }

    public function __toString(): string
    {
        $out_str = "";
        foreach ($this->classifications as $classification_name => $classification_value) {
            $out_str .= ":$classification_name: $classification_value\n";
        }
        foreach ($this->fields as $field_name => $field_value) {
            $out_str .= ":$field_name: $$field_value\n";
        }
        return trim($out_str);
    }
}
