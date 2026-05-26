<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Us\HealthcareCard;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Copayments for covered services.
 */
class HealthcareCardV1Copay
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var float|null The price of the service.
     */
    public ?float $serviceFees;
    /**
     * @var string|null The name of the service.
     */
    public ?string $serviceName;
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
        $this->pageId = $pageId;
        $this->serviceFees = isset($rawPrediction["service_fees"])
            ? (float) ($rawPrediction["service_fees"]) : null;
        $this->serviceName = $rawPrediction["service_name"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["serviceFees"] = SummaryHelperV1::formatFloat($this->serviceFees);
        $outArr["serviceName"] = SummaryHelperV1::formatForDisplay($this->serviceName, 20);
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
        $outStr .= SummaryHelperV1::padString($printable["serviceFees"], 12);
        $outStr .= SummaryHelperV1::padString($printable["serviceName"], 20);
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
