<?php

namespace Mindee\Product\Us\UsMail;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The address of the sender.
 */
class UsMailV2SenderAddress
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string The city of the sender's address.
     */
    public ?string $city;
    /**
     * @var string The complete address of the sender.
     */
    public ?string $complete;
    /**
     * @var string The postal code of the sender's address.
     */
    public ?string $postalCode;
    /**
     * @var string Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.
     */
    public ?string $state;
    /**
     * @var string The street of the sender's address.
     */
    public ?string $street;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->city = $rawPrediction["city"] ?? null;
        $this->complete = $rawPrediction["complete"] ?? null;
        $this->postalCode = $rawPrediction["postal_code"] ?? null;
        $this->state = $rawPrediction["state"] ?? null;
        $this->street = $rawPrediction["street"] ?? null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["city"] = SummaryHelper::formatForDisplay($this->city, 15);
        $outArr["complete"] = SummaryHelper::formatForDisplay($this->complete, 35);
        $outArr["postalCode"] = SummaryHelper::formatForDisplay($this->postalCode);
        $outArr["state"] = SummaryHelper::formatForDisplay($this->state);
        $outArr["street"] = SummaryHelper::formatForDisplay($this->street, 25);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
     * @return string
     */
    public function toFieldList(): string
    {
        $printable = $this->printableValues();
        $outStr = "";
        $outStr .= "\n  :City: " . $printable["city"];
        $outStr .= "\n  :Complete Address: " . $printable["complete"];
        $outStr .= "\n  :Postal Code: " . $printable["postalCode"];
        $outStr .= "\n  :State: " . $printable["state"];
        $outStr .= "\n  :Street: " . $printable["street"];
        return rtrim($outStr);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelper::cleanOutString($this->toTableLine());
    }
}
