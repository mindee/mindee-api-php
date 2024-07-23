<?php

namespace Mindee\Product\Us\HealthcareCard;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Is a fixed amount for a covered service.
 */
class HealthcareCardV1Copay
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float The price of service.
     */
    public ?float $serviceFees;
    /**
     * @var string The name of service of the copay.
     */
    public ?string $serviceName;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->serviceFees = isset($rawPrediction["service_fees"]) ?
        number_format(floatval($rawPrediction["service_fees"]), 2, ".", "") :
        null;
        $this->serviceName = $rawPrediction["service_name"] ?? null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["serviceFees"] = $this->serviceFees == null ? "" : number_format($this->serviceFees, 2, ".", "");
        $outArr["serviceName"] = SummaryHelper::formatForDisplay($this->serviceName);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     * @return string
     */
    public function toTableLine(): string
    {
        $printable = $this->printableValues();
        $outStr = "| ";
        $outStr .= str_pad($printable["serviceFees"], 12) . " | ";
        $outStr .= str_pad($printable["serviceName"], 12) . " | ";
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
