<?php

namespace Mindee\Product\Resume;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The list of the candidate's educational background.
 */
class ResumeV1Education
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The area of study or specialization.
     */
    public ?string $degreeDomain;
    /**
     * @var string|null The type of degree obtained, such as Bachelor's, Master's, or Doctorate.
     */
    public ?string $degreeType;
    /**
     * @var string|null The month when the education program or course was completed.
     */
    public ?string $endMonth;
    /**
     * @var string|null The year when the education program or course was completed.
     */
    public ?string $endYear;
    /**
     * @var string|null The name of the school.
     */
    public ?string $school;
    /**
     * @var string|null The month when the education program or course began.
     */
    public ?string $startMonth;
    /**
     * @var string|null The year when the education program or course began.
     */
    public ?string $startYear;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->degreeDomain = $rawPrediction["degree_domain"] ?? null;
        $this->degreeType = $rawPrediction["degree_type"] ?? null;
        $this->endMonth = $rawPrediction["end_month"] ?? null;
        $this->endYear = $rawPrediction["end_year"] ?? null;
        $this->school = $rawPrediction["school"] ?? null;
        $this->startMonth = $rawPrediction["start_month"] ?? null;
        $this->startYear = $rawPrediction["start_year"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["degreeDomain"] = SummaryHelper::formatForDisplay($this->degreeDomain, 15);
        $outArr["degreeType"] = SummaryHelper::formatForDisplay($this->degreeType, 25);
        $outArr["endMonth"] = SummaryHelper::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelper::formatForDisplay($this->endYear);
        $outArr["school"] = SummaryHelper::formatForDisplay($this->school, 25);
        $outArr["startMonth"] = SummaryHelper::formatForDisplay($this->startMonth);
        $outArr["startYear"] = SummaryHelper::formatForDisplay($this->startYear);
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
        $outArr["degreeDomain"] = SummaryHelper::formatForDisplay($this->degreeDomain);
        $outArr["degreeType"] = SummaryHelper::formatForDisplay($this->degreeType);
        $outArr["endMonth"] = SummaryHelper::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelper::formatForDisplay($this->endYear);
        $outArr["school"] = SummaryHelper::formatForDisplay($this->school);
        $outArr["startMonth"] = SummaryHelper::formatForDisplay($this->startMonth);
        $outArr["startYear"] = SummaryHelper::formatForDisplay($this->startYear);
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
        $outStr .= SummaryHelper::padString($printable["degreeDomain"], 15);
        $outStr .= SummaryHelper::padString($printable["degreeType"], 25);
        $outStr .= SummaryHelper::padString($printable["endMonth"], 9);
        $outStr .= SummaryHelper::padString($printable["endYear"], 8);
        $outStr .= SummaryHelper::padString($printable["school"], 25);
        $outStr .= SummaryHelper::padString($printable["startMonth"], 11);
        $outStr .= SummaryHelper::padString($printable["startYear"], 10);
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
