<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about the employment.
 */
class PayslipV2Employment
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The category of the employment.
     */
    public ?string $category;
    /**
     * @var float|null The coefficient of the employment.
     */
    public ?float $coefficient;
    /**
     * @var string|null The collective agreement of the employment.
     */
    public ?string $collectiveAgreement;
    /**
     * @var string|null The job title of the employee.
     */
    public ?string $jobTitle;
    /**
     * @var string|null The position level of the employment.
     */
    public ?string $positionLevel;
    /**
     * @var string|null The start date of the employment.
     */
    public ?string $startDate;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->category = $rawPrediction["category"] ?? null;
        $this->coefficient = isset($rawPrediction["coefficient"]) ?
            floatval($rawPrediction["coefficient"]) : null;
        $this->collectiveAgreement = $rawPrediction["collective_agreement"] ?? null;
        $this->jobTitle = $rawPrediction["job_title"] ?? null;
        $this->positionLevel = $rawPrediction["position_level"] ?? null;
        $this->startDate = $rawPrediction["start_date"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["category"] = SummaryHelper::formatForDisplay($this->category);
        $outArr["coefficient"] = SummaryHelper::formatFloat($this->coefficient);
        $outArr["collectiveAgreement"] = SummaryHelper::formatForDisplay($this->collectiveAgreement);
        $outArr["jobTitle"] = SummaryHelper::formatForDisplay($this->jobTitle);
        $outArr["positionLevel"] = SummaryHelper::formatForDisplay($this->positionLevel);
        $outArr["startDate"] = SummaryHelper::formatForDisplay($this->startDate);
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
        $outArr["category"] = SummaryHelper::formatForDisplay($this->category);
        $outArr["coefficient"] = SummaryHelper::formatFloat($this->coefficient);
        $outArr["collectiveAgreement"] = SummaryHelper::formatForDisplay($this->collectiveAgreement);
        $outArr["jobTitle"] = SummaryHelper::formatForDisplay($this->jobTitle);
        $outArr["positionLevel"] = SummaryHelper::formatForDisplay($this->positionLevel);
        $outArr["startDate"] = SummaryHelper::formatForDisplay($this->startDate);
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
        $outStr .= "\n  :Category: " . $printable["category"];
        $outStr .= "\n  :Coefficient: " . $printable["coefficient"];
        $outStr .= "\n  :Collective Agreement: " . $printable["collectiveAgreement"];
        $outStr .= "\n  :Job Title: " . $printable["jobTitle"];
        $outStr .= "\n  :Position Level: " . $printable["positionLevel"];
        $outStr .= "\n  :Start Date: " . $printable["startDate"];
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
