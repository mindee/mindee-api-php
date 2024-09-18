<?php

namespace Mindee\Product\BillOfLading;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The shipping company responsible for transporting the goods.
 */
class BillOfLadingV1Carrier
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

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
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->name = $rawPrediction["name"] ?? null;
        $this->professionalNumber = $rawPrediction["professional_number"] ?? null;
        $this->scac = $rawPrediction["scac"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name);
        $outArr["professionalNumber"] = SummaryHelper::formatForDisplay($this->professionalNumber);
        $outArr["scac"] = SummaryHelper::formatForDisplay($this->scac);
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
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name);
        $outArr["professionalNumber"] = SummaryHelper::formatForDisplay($this->professionalNumber);
        $outArr["scac"] = SummaryHelper::formatForDisplay($this->scac);
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
        return SummaryHelper::cleanOutString($this->toFieldList());
    }
}
