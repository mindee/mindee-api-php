<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference\Field;

use Mindee\Error\MindeeAPIException;
use Stringable;

use function array_key_exists;
use function sprintf;

/**
 * Base class for V2 fields.
 */
abstract class BaseField implements Stringable
{
    /**
     * @var array<FieldLocation> List of possible locations for a field.
     */
    public array $locations;

    /**
     * @var FieldConfidence|null Confidence score for the field.
     */
    public ?FieldConfidence $confidence;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer $indentLevel Level of indentation for rst display.
     */
    public function __construct(array $rawPrediction, protected int $indentLevel = 0)
    {
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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer $indentLevel Level of indentation for rst display.
     * @throws MindeeAPIException Throws if the field type isn't recognized.
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
        throw new MindeeAPIException(
            sprintf('Unrecognized field format in %s.', json_encode($rawPrediction))
        );
    }

    /**
     * Base str-rep. Do not use.
     * @throws MindeeAPIException
     */
    public function __toString(): string
    {
        throw new MindeeAPIException('Not implemented');
    }
}
