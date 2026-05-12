<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The list of the candidate's educational background.
 */
class ResumeV1Education
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @param array $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
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
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["degreeDomain"] = SummaryHelperV1::formatForDisplay($this->degreeDomain, 15);
        $outArr["degreeType"] = SummaryHelperV1::formatForDisplay($this->degreeType, 25);
        $outArr["endMonth"] = SummaryHelperV1::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelperV1::formatForDisplay($this->endYear);
        $outArr["school"] = SummaryHelperV1::formatForDisplay($this->school, 25);
        $outArr["startMonth"] = SummaryHelperV1::formatForDisplay($this->startMonth);
        $outArr["startYear"] = SummaryHelperV1::formatForDisplay($this->startYear);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["degreeDomain"] = SummaryHelperV1::formatForDisplay($this->degreeDomain);
        $outArr["degreeType"] = SummaryHelperV1::formatForDisplay($this->degreeType);
        $outArr["endMonth"] = SummaryHelperV1::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelperV1::formatForDisplay($this->endYear);
        $outArr["school"] = SummaryHelperV1::formatForDisplay($this->school);
        $outArr["startMonth"] = SummaryHelperV1::formatForDisplay($this->startMonth);
        $outArr["startYear"] = SummaryHelperV1::formatForDisplay($this->startYear);
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
        $outStr .= SummaryHelperV1::padString($printable["degreeDomain"], 15);
        $outStr .= SummaryHelperV1::padString($printable["degreeType"], 25);
        $outStr .= SummaryHelperV1::padString($printable["endMonth"], 9);
        $outStr .= SummaryHelperV1::padString($printable["endYear"], 8);
        $outStr .= SummaryHelperV1::padString($printable["school"], 25);
        $outStr .= SummaryHelperV1::padString($printable["startMonth"], 11);
        $outStr .= SummaryHelperV1::padString($printable["startYear"], 10);
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
