<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Us\UsMail;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;
use Stringable;

/**
 * The addresses of the recipients.
 */
class UsMailV3RecipientAddress implements Stringable
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @var string|null The unit number of the recipient's address.
     */
    public ?string $unit;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, public ?int $pageId)
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
        $this->unit = $rawPrediction["unit"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["city"] = SummaryHelperV1::formatForDisplay($this->city, 15);
        $outArr["complete"] = SummaryHelperV1::formatForDisplay($this->complete, 35);
        $outArr["isAddressChange"] = SummaryHelperV1::formatForDisplay($this->isAddressChange);
        $outArr["postalCode"] = SummaryHelperV1::formatForDisplay($this->postalCode);
        $outArr["privateMailboxNumber"] = SummaryHelperV1::formatForDisplay($this->privateMailboxNumber);
        $outArr["state"] = SummaryHelperV1::formatForDisplay($this->state);
        $outArr["street"] = SummaryHelperV1::formatForDisplay($this->street, 25);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit, 15);
        return $outArr;
    }

    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     */
    public function toTableLine(): string
    {
        $printable = $this->tablePrintableValues();
        $outStr = "| ";
        $outStr .= SummaryHelperV1::padString($printable["city"], 15);
        $outStr .= SummaryHelperV1::padString($printable["complete"], 35);
        $outStr .= SummaryHelperV1::padString($printable["isAddressChange"], 17);
        $outStr .= SummaryHelperV1::padString($printable["postalCode"], 11);
        $outStr .= SummaryHelperV1::padString($printable["privateMailboxNumber"], 22);
        $outStr .= SummaryHelperV1::padString($printable["state"], 5);
        $outStr .= SummaryHelperV1::padString($printable["street"], 25);
        $outStr .= SummaryHelperV1::padString($printable["unit"], 15);
        return rtrim(SummaryHelperV1::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::cleanOutString($this->toTableLine());
    }
}
