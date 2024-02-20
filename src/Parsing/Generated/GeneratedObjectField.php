<?php

namespace Mindee\Parsing\Generated;

use Mindee\Parsing\Standard\PositionField;

/**
 * A JSON-like object, with miscellaneous values.
 */
class GeneratedObjectField
{
    /** @var integer|null Id of the page the object was found on */
    private ?int $pageId;

    /** @var float|null Confidence with which the value was assessed */
    private ?float $confidence;

    /** @var string|null Raw unprocessed value, as it was sent by the server */
    private ?string $rawValue;

    /** @var array List of all printable field names */
    private array $printableValues = [];

    /**
     * Constructor.
     *
     * @param array        $rawPrediction Raw prediction data.
     * @param integer|null $pageId        Id of the page.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $itemPageId = null;
        $this->printableValues = [];

        foreach ($rawPrediction as $name => $value) {
            if ($name === "page_id") {
                $itemPageId = $value;
            } elseif (in_array($name, ["polygon", "rectangle", "quadrangle", "bounding_box"], true)) {
                $this->{$name} = new PositionField([$name => $value], $name, $itemPageId);
                $this->printableValues[] = $name;
            } elseif ($name === "confidence") {
                $this->confidence = $value;
            } elseif ($name === "raw_value") {
                $this->rawValue = $value;
            } else {
                $this->{$name} = $value !== null ? (string)$value : null;
                $this->printableValues[] = $name;
            }
            $this->pageId = $pageId ?? $itemPageId;
        }
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
    private function strLevel(int $level = 0): string
    {
        $indent = "  " . str_repeat("  ", $level);
        $outStr = "";
        foreach ($this->printableValues as $attr) {
            $value = $this->{$attr};
            $strValue = $value !== null ? (string)$value : "";
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
