<?php

namespace Mindee\Product\Us\HealthcareCard;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Copayments for covered services.
 */
class HealthcareCardV1Copay
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null The price of the service.
     */
    public ?float $serviceFees;
    /**
     * @var string|null The name of the service.
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
            floatval($rawPrediction["service_fees"]) : null;
        $this->serviceName = $rawPrediction["service_name"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["serviceFees"] = SummaryHelper::formatFloat($this->serviceFees);
        $outArr["serviceName"] = SummaryHelper::formatForDisplay($this->serviceName, 20);
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
        $outArr["serviceFees"] = SummaryHelper::formatFloat($this->serviceFees);
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
        $printable = $this->tablePrintableValues();
        $outStr = "| ";
        $outStr .= SummaryHelper::padString($printable["serviceFees"], 12);
        $outStr .= SummaryHelper::padString($printable["serviceName"], 20);
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
