<?php

namespace Mindee\V1\Product\BillOfLading;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The party responsible for shipping the goods.
 */
class BillOfLadingV1Shipper
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The address of the shipper.
     */
    public ?string $address;
    /**
     * @var string|null The  email of the shipper.
     */
    public ?string $email;
    /**
     * @var string|null The name of the shipper.
     */
    public ?string $name;
    /**
     * @var string|null The phone number of the shipper.
     */
    public ?string $phone;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->address = $rawPrediction["address"] ?? null;
        $this->email = $rawPrediction["email"] ?? null;
        $this->name = $rawPrediction["name"] ?? null;
        $this->phone = $rawPrediction["phone"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelperV1::formatForDisplay($this->address);
        $outArr["email"] = SummaryHelperV1::formatForDisplay($this->email);
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name);
        $outArr["phone"] = SummaryHelperV1::formatForDisplay($this->phone);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelperV1::formatForDisplay($this->address);
        $outArr["email"] = SummaryHelperV1::formatForDisplay($this->email);
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name);
        $outArr["phone"] = SummaryHelperV1::formatForDisplay($this->phone);
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
        $outStr .= "\n  :Address: " . $printable["address"];
        $outStr .= "\n  :Email: " . $printable["email"];
        $outStr .= "\n  :Name: " . $printable["name"];
        $outStr .= "\n  :Phone: " . $printable["phone"];
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
