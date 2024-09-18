<?php

namespace Mindee\Product\Us\UsMail;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The addresses of the recipients.
 */
class UsMailV2RecipientAddress
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The city of the recipient's address.
     */
    public ?string $city;
    /**
     * @var string|null The complete address of the recipient.
     */
    public ?string $complete;
    /**
     * @var boolean Indicates if the recipient's address is a change of address.
     */
    public bool $isAddressChange;
    /**
     * @var string|null The postal code of the recipient's address.
     */
    public ?string $postalCode;
    /**
     * @var string|null The private mailbox number of the recipient's address.
     */
    public ?string $privateMailboxNumber;
    /**
     * @var string|null Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.
     */
    public ?string $state;
    /**
     * @var string|null The street of the recipient's address.
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
        $this->isAddressChange = $rawPrediction["is_address_change"] ?? null;
        $this->postalCode = $rawPrediction["postal_code"] ?? null;
        $this->privateMailboxNumber = $rawPrediction["private_mailbox_number"] ?? null;
        $this->state = $rawPrediction["state"] ?? null;
        $this->street = $rawPrediction["street"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["city"] = SummaryHelper::formatForDisplay($this->city, 15);
        $outArr["complete"] = SummaryHelper::formatForDisplay($this->complete, 35);
        $outArr["isAddressChange"] = SummaryHelper::formatForDisplay($this->isAddressChange);
        $outArr["postalCode"] = SummaryHelper::formatForDisplay($this->postalCode);
        $outArr["privateMailboxNumber"] = SummaryHelper::formatForDisplay($this->privateMailboxNumber);
        $outArr["state"] = SummaryHelper::formatForDisplay($this->state);
        $outArr["street"] = SummaryHelper::formatForDisplay($this->street, 25);
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
        $outArr["city"] = SummaryHelper::formatForDisplay($this->city);
        $outArr["complete"] = SummaryHelper::formatForDisplay($this->complete);
        $outArr["isAddressChange"] = SummaryHelper::formatForDisplay($this->isAddressChange);
        $outArr["postalCode"] = SummaryHelper::formatForDisplay($this->postalCode);
        $outArr["privateMailboxNumber"] = SummaryHelper::formatForDisplay($this->privateMailboxNumber);
        $outArr["state"] = SummaryHelper::formatForDisplay($this->state);
        $outArr["street"] = SummaryHelper::formatForDisplay($this->street);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     * @return string
     */
    public function toTableLine(): string
    {
        $printable = $this->tablePrintableValues();
        $outStr = "| ";
        $outStr .= mb_str_pad($printable["city"], 15, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["complete"], 35, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["isAddressChange"], 17, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["postalCode"], 11, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["privateMailboxNumber"], 22, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["state"], 5, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["street"], 25, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        return rtrim(SummaryHelper::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelper::cleanOutString($this->toTableLine());
    }
}
