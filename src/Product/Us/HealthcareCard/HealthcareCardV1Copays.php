<?php

namespace Mindee\Product\Us\HealthcareCard;

/**
 * Is a fixed amount for a covered service.
 */
class HealthcareCardV1Copays extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new HealthcareCardV1Copay($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function copaysSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 14);
        return $outStr . "+";
    }


    /**
     * String representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        $lines = "";
        $iterator = $this->getIterator();
        if (!$iterator->valid()) {
            return "";
        }
        while ($iterator->valid()) {
            $entry = $iterator->current();
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::copaysSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::copaysSeparator('-') . "\n ";
        $outStr .= " | Service Fees";
        $outStr .= " | Service Name";
        $outStr .= " |\n" . self::copaysSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
