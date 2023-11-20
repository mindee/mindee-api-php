<?php

namespace Mindee\Parsing\Custom;

use Mindee\Error\MindeeException;
use Mindee\Geometry\BBox;
use function Mindee\Geometry\generateBBoxFromPolygon;
use function Mindee\Geometry\generateBBoxFromPolygons;
use function Mindee\Geometry\get_min_max_y;

class CustomLine
{
    public int $row_number;
    public array $fields;
    public BBox $bbox;

    public function __construct(
        int $row_number
    ) {
        $this->row_number = $row_number;
        $this->bbox = new BBox(1, 1, 0, 0);
        $this->fields = [];
    }

    public function updateField(string $field_name, ListFieldValue $field_value)
    {
        if (array_key_exists($field_name, $this->fields)) {
            $existing_field = $this->fields[$field_name];
            $existing_content = $existing_field->content;
            $merged_content = '';
            if (count($existing_content) > 0) {
                $merged_content .= $existing_content.' ';
            }
            $merged_content .= $field_value->content;
            $merged_polygon = generateBBoxFromPolygons([$existing_field->polygon, $field_value->polygon]);
            $merged_confidence = $existing_field->confidence * $field_value->confidence;
        } else {
            $merged_content = $field_value->content;
            $merged_confidence = $field_value->confidence;
            $merged_polygon = generateBBoxFromPolygon($field_value->polygon);
        }
        $this->fields[$field_name] = new ListFieldValue([
            'content' => $merged_content,
            'confidence' => $merged_confidence,
            'polygon' => $merged_polygon,
        ]);
    }

    public static function isBBoxInLine(
        CustomLine $line,
        BBox       $bbox,
        float      $height_line_tolerance
    ): bool
    {
        if (abs($bbox->getMinY() - $line->bbox->getMinY()) <= $height_line_tolerance) {
            return true;
        }

        return abs($line->bbox->getMinY() - $bbox->getMinY()) <= $height_line_tolerance;
    }

    public static function prepare(
        string $anchor_name,
        array $fields,
        float $height_line_tolerance
    ): array {
        $lines_prepared = [];
        if (array_key_exists($anchor_name, $fields)) {
            $anchor_field = $fields[$anchor_name];
        } else {
            throw new MindeeException('No lines have been detected.');
        }
        $current_line_number = 1;
        $current_line = new CustomLine($current_line_number);
        if ($anchor_field && count($anchor_field->values) > 0) {
            $current_value = $anchor_field->values[0];
            $current_line->bbox->extendWith($current_value->polygon);
        }
        for ($i = 1; $i < count($anchor_field->values); ++$i) {
            $current_value = $anchor_field->values[$i];
            $current_field_box = generateBBoxFromPolygon($current_value->polygon);
            if (!CustomLine::isBBoxInLine(
                $current_line,
                $current_field_box,
                $height_line_tolerance
            )) {
                $lines_prepared[] = $current_line;
                ++$current_line_number;
                $current_line = new CustomLine($current_line_number);
                $current_line->bbox->extendWith($current_value->polygon);
            }
        }
        $count_valid_lines = 0;
        foreach ($lines_prepared as $line) {
            if ($line->row_number == $current_line_number) {
                ++$count_valid_lines;
            }
        }
        if ($count_valid_lines == 0) {
            $lines_prepared[] = $current_line;
        }

        return $lines_prepared;
    }

    private static function findBestAnchor(
        array $anchors,
        array $fields
    ): string {
        $anchor = '';
        $anchor_rows = 0;
        foreach ($anchors as $field_anchor) {
            $values = $fields[$field_anchor]->values;
            if ($values && count($values) > $anchor_rows) {
                $anchor_rows = count($values);
                $anchor = $field_anchor;
            }
        }

        return $anchor;
    }

    public static function getLineItems(
        array $anchors,
        array $field_names,
        array $fields,
        float $height_line_tolerance = 0.01
    ): array {
        $line_items = [];
        $fields_to_transform = [];
        foreach ($fields as $field_name => $field_value) {
            if (array_key_exists($field_name, $field_names)) {
                $fields_to_transform[$field_name] = $field_value;
            }
        }
        $anchor = CustomLine::findBestAnchor($anchors, $fields_to_transform);
        if (!$anchor) {
            error_log('Could not find an anchor!');

            return $line_items;
        }

        $lines_prepared = CustomLine::prepare(
            $anchor,
            $fields_to_transform,
            $height_line_tolerance
        );

        foreach ($lines_prepared as $current_line) {
            foreach ($fields_to_transform as $field_name => $field) {
                foreach ($field->values as $list_field_value) {
                    $min_max_y = get_min_max_y($list_field_value->polygon);
                    if (
                        abs($min_max_y->getMax() - $current_line->bbox->y_max) <=
                        $height_line_tolerance &&
                        abs($min_max_y->getMin() - $current_line->bbox->y_min) <=
                        $height_line_tolerance
                        ) {
                        $current_line->updateField($field_name, $list_field_value);
                    }
                }
            }
        }

        return $lines_prepared;
    }
}
