<?php

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Custom\ClassificationField;
use Mindee\Parsing\Custom\CustomLine;
use Mindee\Parsing\Custom\ListField;

/**
 * Custom V1 document prediction results.
 */
class CustomV1Document extends Prediction
{
    /**
     * @var array Array of all fields in the document.
     */
    public array $fields;
    /**
     * @var array Array of all classifications in the document.
     */
    public array $classifications;

    /**
     * @param array $raw_prediction Array containing the JSON document response.
     */
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

    /**
     * Order column fields into line items.
     *
     * @param array $anchor_names     List of possible anchor fields.
     * @param array $field_names      List of all column fields.
     * @param float $height_tolerance Height tolerance to apply to lines.
     * @return array
     */
    public function columnsToLineItems(array $anchor_names, array $field_names, float $height_tolerance): array
    {
        return CustomLine::getLineItems(
            $anchor_names,
            $field_names,
            $this->fields,
            $height_tolerance
        );
    }

    /**
     * @return string String representation.
     */
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
