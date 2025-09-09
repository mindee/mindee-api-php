<?php

namespace Mindee\Parsing\V2\Field;

use Mindee\Error\MindeeApiException;

/**
 * Base class for V2 fields.
 */
abstract class BaseField
{
    /**
     * @var integer Level of indentation for rst display.
     */
    protected int $indentLevel;
    /**
     * @var array<FieldLocation> List of possible locations for a field.
     */
    public array $locations;

    /**
     * @var FieldConfidence|null Confidence score for the field.
     */
    public ?FieldConfidence $confidence;

    /**
     * @param array   $rawPrediction Raw prediction array.
     * @param integer $indentLevel   Level of indentation for rst display.
     */
    public function __construct(array $rawPrediction, int $indentLevel = 0)
    {
        $this->indentLevel = $indentLevel;
        if (array_key_exists("locations", $rawPrediction) && $rawPrediction["locations"]) {
            $this->locations = [];
            foreach ($rawPrediction["locations"] as $location) {
                $this->locations[] = new FieldLocation($location);
            }
        }
        if (array_key_exists("confidence", $rawPrediction) && $rawPrediction["confidence"]) {
            $this->confidence = FieldConfidence::from($rawPrediction["confidence"]);
        }
    }

    /**
     * @param array   $rawPrediction Raw prediction array.
     * @param integer $indentLevel   Level of indentation for rst display.
     * @return ListField|ObjectField|SimpleField
     * @throws MindeeApiException Throws if the field type isn't recognized.
     */
    public static function createField(array $rawPrediction, int $indentLevel = 0): ListField|ObjectField|SimpleField
    {
        if (array_key_exists('items', $rawPrediction)) {
            return new ListField($rawPrediction, $indentLevel);
        }
        if (array_key_exists('fields', $rawPrediction)) {
            return new ObjectField($rawPrediction, $indentLevel);
        }
        if (array_key_exists('value', $rawPrediction)) {
            return new SimpleField($rawPrediction, $indentLevel);
        }
        throw new MindeeApiException(
            sprintf('Unrecognized field format in %s.', json_encode($rawPrediction))
        );
    }
}
