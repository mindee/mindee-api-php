<?php

declare(strict_types=1);

namespace Mindee\V1\Product\BillOfLading;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The shipping company responsible for transporting the goods.
 */
class BillOfLadingV1Carrier
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The name of the carrier.
     */
    public ?string $name;
    /**
     * @var string|null The professional number of the carrier.
     */
    public ?string $professionalNumber;
    /**
     * @var string|null The Standard Carrier Alpha Code (SCAC) of the carrier.
     */
    public ?string $scac;
    /**
     * @var integer|null Page ID.
     */
    public ?int $pageId;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->name = $rawPrediction["name"] ?? null;
        $this->professionalNumber = $rawPrediction["professional_number"] ?? null;
        $this->scac = $rawPrediction["scac"] ?? null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name);
        $outArr["professionalNumber"] = SummaryHelperV1::formatForDisplay($this->professionalNumber);
        $outArr["scac"] = SummaryHelperV1::formatForDisplay($this->scac);
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
        $outStr .= "\n  :Name: " . $printable["name"];
        $outStr .= "\n  :Professional Number: " . $printable["professionalNumber"];
        $outStr .= "\n  :SCAC: " . $printable["scac"];
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
