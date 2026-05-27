<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\EnergyBill;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;
use Stringable;

/**
 * The company that supplies the energy.
 */
class EnergyBillV1EnergySupplier implements Stringable
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The address of the energy supplier.
     */
    public ?string $address;
    /**
     * @var string|null The name of the energy supplier.
     */
    public ?string $name;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, public ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->address = $rawPrediction["address"] ?? null;
        $this->name = $rawPrediction["name"] ?? null;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["address"] = SummaryHelperV1::formatForDisplay($this->address);
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name);
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
        $outStr .= "\n  :Name: " . $printable["name"];
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
