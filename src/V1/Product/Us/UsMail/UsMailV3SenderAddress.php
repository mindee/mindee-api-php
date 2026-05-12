<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Us\UsMail;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The address of the sender.
 */
class UsMailV3SenderAddress
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The city of the sender's address.
     */
    public ?string $city;
    /**
     * @var string|null The complete address of the sender.
     */
    public ?string $complete;
    /**
     * @var string|null The postal code of the sender's address.
     */
    public ?string $postalCode;
    /**
     * @var string|null Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.
     */
    public ?string $state;
    /**
     * @var string|null The street of the sender's address.
     */
    public ?string $street;

    /**
     * @param array $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
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
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["city"] = SummaryHelperV1::formatForDisplay($this->city, 15);
        $outArr["complete"] = SummaryHelperV1::formatForDisplay($this->complete, 35);
        $outArr["postalCode"] = SummaryHelperV1::formatForDisplay($this->postalCode);
        $outArr["state"] = SummaryHelperV1::formatForDisplay($this->state);
        $outArr["street"] = SummaryHelperV1::formatForDisplay($this->street, 25);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["city"] = SummaryHelperV1::formatForDisplay($this->city);
        $outArr["complete"] = SummaryHelperV1::formatForDisplay($this->complete);
        $outArr["postalCode"] = SummaryHelperV1::formatForDisplay($this->postalCode);
        $outArr["state"] = SummaryHelperV1::formatForDisplay($this->state);
        $outArr["street"] = SummaryHelperV1::formatForDisplay($this->street);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
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
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
