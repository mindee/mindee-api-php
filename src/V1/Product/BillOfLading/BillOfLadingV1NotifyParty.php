<?php

declare(strict_types=1);

namespace Mindee\V1\Product\BillOfLading;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The party to be notified of the arrival of the goods.
 */
class BillOfLadingV1NotifyParty
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The address of the notify party.
     */
    public ?string $address;
    /**
     * @var string|null The  email of the shipper.
     */
    public ?string $email;
    /**
     * @var string|null The name of the notify party.
     */
    public ?string $name;
    /**
     * @var string|null The phone number of the notify party.
     */
    public ?string $phone;
    /**
     * @var integer|null Page ID.
     */
    public ?int $pageId;

    /**
     * @param array<string, mixed> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->address = $rawPrediction["address"] ?? null;
        $this->email = $rawPrediction["email"] ?? null;
        $this->name = $rawPrediction["name"] ?? null;
        $this->phone = $rawPrediction["phone"] ?? null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing inside an RST table.
     *
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
     * @return array<string, string>
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
