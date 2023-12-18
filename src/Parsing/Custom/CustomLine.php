<?php

namespace Mindee\Parsing\Custom;

use Mindee\Error\MindeeException;
use Mindee\Geometry\BBox;
use Mindee\Geometry\BBoxUtils;
use Mindee\Geometry\MinMaxUtils;

/**
 * Represents a single line.
 */
class CustomLine
{
    /**
     * @var integer Index of the row for a given line.
     */
    public int $rowNumber;
    /**
     * @var array Fields contained in the line.
     */
    public array $fields;
    /**
     * @var \Mindee\Geometry\BBox Simplified bounding box of the line.
     */
    public BBox $bbox;

    /**
     * @param integer $rowNumber Index of the row.
     */
    public function __construct(
        int $rowNumber
    ) {
        $this->rowNumber = $rowNumber;
        $this->bbox = new BBox(1, 0, 1, 0);
        $this->fields = [];
    }

    /**
     * Updates a field's value.
     *
     * @param string                                $fieldName  Name of the field.
     * @param \Mindee\Parsing\Custom\ListFieldValue $fieldValue Value of the field.
     * @return void
     */
    public function updateField(string $fieldName, ListFieldValue $fieldValue)
    {
        if (array_key_exists($fieldName, $this->fields)) {
            $existingField = $this->fields[$fieldName];
            $existingContent = $existingField->content;
            $mergedContent = '';
            if (strlen($existingContent) > 0) {
                $mergedContent .= $existingContent . ' ';
            }
            $mergedContent .= $fieldValue->content;
            $mergedPolygon = BBoxUtils::generateBBoxFromPolygons([$existingField->polygon, $fieldValue->polygon]);
            $mergedConfidence = $existingField->confidence * $fieldValue->confidence;
        } else {
            $mergedContent = $fieldValue->content;
            $mergedConfidence = $fieldValue->confidence;
            $mergedPolygon = BBoxUtils::generateBBoxFromPolygon($fieldValue->polygon);
        }
        $this->fields[$fieldName] = new ListFieldValue([
            'content' => $mergedContent,
            'confidence' => $mergedConfidence,
            'polygon' => $mergedPolygon,
        ]);
    }

    /**
     * Checks if a BBox is in a given line.
     *
     * @param \Mindee\Parsing\Custom\CustomLine $line                Current line to check.
     * @param \Mindee\Geometry\BBox             $bbox                BBox.
     * @param float                             $heightLineTolerance Height tolerance in pixels.
     * @return boolean
     */
    public static function isBBoxInLine(
        CustomLine $line,
        BBox $bbox,
        float $heightLineTolerance
    ): bool {
        if (abs($bbox->getMinY() - $line->bbox->getMinY()) <= $heightLineTolerance) {
            return true;
        }

        return abs($line->bbox->getMinY() - $bbox->getMinY()) <= $heightLineTolerance;
    }

    /**
     * Prepares the line items before filling them.
     *
     * @param string $anchorName          Name of the anchor.
     * @param array  $fields              List of fields.
     * @param float  $heightLineTolerance Height tolerance in pixels.
     * @return array
     * @throws \Mindee\Error\MindeeException Throws if no lines have been found.
     */
    public static function prepare(
        string $anchorName,
        array $fields,
        float $heightLineTolerance
    ): array {
        $linesPrepared = [];
        if (array_key_exists($anchorName, $fields)) {
            $anchorField = $fields[$anchorName];
        } else {
            throw new MindeeException('No lines have been detected.');
        }
        $currentLineNumber = 1;
        $currentLine = new CustomLine($currentLineNumber);
        if ($anchorField && count($anchorField->values) > 0) {
            $currentValue = $anchorField->values[0];
            $currentLine->bbox->extendWith($currentValue->polygon);
        }
        for ($i = 1; $i < count($anchorField->values); ++$i) {
            $currentValue = $anchorField->values[$i];
            $currentFieldBox = BBoxUtils::generateBBoxFromPolygon($currentValue->polygon);
            if (
                !CustomLine::isBBoxInLine(
                    $currentLine,
                    $currentFieldBox,
                    $heightLineTolerance
                )
            ) {
                $linesPrepared[] = $currentLine;
                ++$currentLineNumber;
                $currentLine = new CustomLine($currentLineNumber);
                $currentLine->bbox->extendWith($currentValue->polygon);
            }
        }
        $countValidLines = 0;
        foreach ($linesPrepared as $line) {
            if ($line->rowNumber == $currentLineNumber) {
                ++$countValidLines;
            }
        }
        if ($countValidLines == 0) {
            $linesPrepared[] = $currentLine;
        }

        return $linesPrepared;
    }

    /**
     * Finds the best anchor for a given array of fields.
     *
     * @param array $anchors Array of potential anchors.
     * @param array $fields  Array of fields.
     * @return string
     */
    private static function findBestAnchor(
        array $anchors,
        array $fields
    ): string {
        $anchor = '';
        $anchorRows = 0;
        foreach ($anchors as $field) {
            $values = $fields[$field]->values;
            if ($values && count($values) > $anchorRows) {
                $anchorRows = count($values);
                $anchor = $field;
            }
        }

        return $anchor;
    }

    /**
     * Creates the line items.
     *
     * @param array $anchors             List of anchor candidates.
     * @param array $fieldNames          List of field names.
     * @param array $fields              List of all fields.
     * @param float $heightLineTolerance Height tolerance in pixels.
     * @return array
     */
    public static function getLineItems(
        array $anchors,
        array $fieldNames,
        array $fields,
        float $heightLineTolerance = 0.01
    ): array {
        $lineItems = [];
        $fieldsToTransform = [];
        foreach ($fields as $fieldName => $fieldValue) {
            if (in_array($fieldName, $fieldNames)) {
                $fieldsToTransform[$fieldName] = $fieldValue;
            }
        }
        $anchor = CustomLine::findBestAnchor($anchors, $fieldsToTransform);
        if (!$anchor) {
            error_log('Could not find an anchor!');

            return $lineItems;
        }

        $linesPrepared = CustomLine::prepare(
            $anchor,
            $fieldsToTransform,
            $heightLineTolerance
        );

        foreach ($linesPrepared as $currentLine) {
            foreach ($fieldsToTransform as $fieldName => $field) {
                foreach ($field->values as $listFieldValue) {
                    $minMaxY = MinMaxUtils::getMinMaxY($listFieldValue->polygon->getCoordinates());
                    if (
                        (abs($minMaxY->getMax() - $currentLine->bbox->getMaxY()) <=
                            $heightLineTolerance) &&
                        (abs($minMaxY->getMin() - $currentLine->bbox->getMinY()) <=
                            $heightLineTolerance)
                    ) {
                        $currentLine->updateField($fieldName, $listFieldValue);
                    }
                }
            }
        }

        return $linesPrepared;
    }
}
