<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Generated;

use Mindee\V1\Parsing\Standard\PositionField;

use function in_array;
use function is_array;
use function is_float;
use function is_int;

/**
 * A JSON-like object, with miscellaneous values.
 */
class GeneratedObjectField
{
    /** @var integer|null ID of the page the object was found on */
    public ?int $pageId;

    /** @var float|null Confidence with which the value was assessed */
    public ?float $confidence;

    /** @var string|null Raw unprocessed value, as it was sent by the server */
    private ?string $rawValue;

    /** @var array List of all printable field names */
    private array $printableValues;
    /** @var array Storage for dynamically generated properties */
    private array $dynamicProperties = [];

    /**
     * Constructor.
     *
     * @param array $rawPrediction Raw prediction data.
     * @param integer|null $pageId ID of the page.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $itemPageId = null;
        $this->printableValues = [];

        foreach ($rawPrediction as $fieldName => $value) {
            if ($fieldName === "page_id") {
                $itemPageId = $value;
            } elseif (in_array($fieldName, ["polygon", "rectangle", "quadrangle", "bounding_box"], true)) {
                $this->{$fieldName} = new PositionField([$fieldName => $value], $itemPageId, false, $fieldName);
                $this->printableValues[] = $fieldName;
            } elseif ($fieldName === "confidence") {
                $this->confidence = $value;
            } elseif ($fieldName === "raw_value") {
                $this->rawValue = $value;
            } else {
                if (isset($value)) {
                    if ((is_int($value) || (is_float($value) && floor($value) === $value)) && (float) $value !== 0.0) {
                        $this->{$fieldName} = $value . ".0";
                    } else {
                        if (is_array($value)) {
                            $this->{$fieldName} = implode(", ", $value);
                        } else {
                            $this->{$fieldName} = (string) $value;
                        }
                    }
                } else {
                    $this->{$fieldName} = null;
                }
                $this->printableValues[] = $fieldName;
            }
            $this->pageId = $pageId ?? $itemPageId;
        }
    }

    /**
     * Magic getter for dynamic properties.
     *
     * @param string $name Property name.
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->dynamicProperties[$name] ?? null;
    }

    /**
     * Magic setter for dynamic properties.
     *
     * @param string $name  Property name.
     * @param mixed  $value Property value.
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->dynamicProperties[$name] = $value;
    }

    /**
     * Magic isset for dynamic properties.
     *
     * @param string $name Property name.
     * @return boolean
     */
    public function __isset(string $name): bool
    {
        return isset($this->dynamicProperties[$name]);
    }

    /**
     * Get a string representation of the object.
     *
     * @return string String representation of the object.
     */
    public function __toString(): string
    {
        return $this->strLevel();
    }

    /**
     * ReSTructured-compliant string representation.
     *
     * Takes into account level of indentation & displays elements as list elements.
     *
     * @param integer $level Level of indentation (times 2 spaces).
     * @return string ReSTructured-compliant string representation.
     */
    public function strLevel(int $level = 0): string
    {
        $indent = "  " . str_repeat("  ", $level);
        $outStr = "";
        foreach ($this->printableValues as $attr) {
            $value = $this->{$attr};
            $strValue = $value !== null ? (string) $value : "";
            $outStr .= "\n{$indent}:{$attr}: {$strValue}";
        }
        return "\n" . $indent . trim($outStr);
    }

    /**
     * Checks whether a field is a custom object or not.
     *
     * @param array $strDict Input dictionary to check.
     * @return boolean Whether the field is a custom object.
     */
    public static function isGeneratedObject(array $strDict): bool
    {
        $commonKeys = [
            "value",
            "polygon",
            "rectangle",
            "page_id",
            "confidence",
            "quadrangle",
            "values",
            "raw_value",
        ];
        foreach (array_keys($strDict) as $key) {
            if (!in_array($key, $commonKeys, true)) {
                return true;
            }
        }
        return false;
    }
}
