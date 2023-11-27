<?php

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Custom\ListField;

/**
 * Custom V1 page prediction results.
 */
class CustomV1Page extends Prediction
{
    /**
     * @var array Dictionary of all fields in the document.
     */
    public array $fields;

    /**
     * @param array $raw_prediction Dictionary containing the JSON document response.
     */
    public function __construct(array $raw_prediction)
    {
        $this->fields = [];
        foreach ($raw_prediction as $field_name => $field_contents) {
            $this->fields[$field_name] = new ListField($field_contents);
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $out_str = "";
        foreach ($this->fields as $field_name => $field_value) {
            $out_str .= ":$field_name: $field_value\n";
        }
        return trim($out_str);
    }
}
