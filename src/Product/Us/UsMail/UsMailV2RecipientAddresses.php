<?php

namespace Mindee\Product\Us\UsMail;

/**
 * The addresses of the recipients.
 */
class UsMailV2RecipientAddresses extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new UsMailV2RecipientAddress($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function recipientAddressesSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 17);
        $outStr .= "+" . str_repeat($char, 37);
        $outStr .= "+" . str_repeat($char, 19);
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 24);
        $outStr .= "+" . str_repeat($char, 7);
        $outStr .= "+" . str_repeat($char, 27);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::recipientAddressesSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::recipientAddressesSeparator('-') . "\n ";
        $outStr .= " | City           ";
        $outStr .= " | Complete Address                   ";
        $outStr .= " | Is Address Change";
        $outStr .= " | Postal Code";
        $outStr .= " | Private Mailbox Number";
        $outStr .= " | State";
        $outStr .= " | Street                   ";
        $outStr .= " |\n" . self::recipientAddressesSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
