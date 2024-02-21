<?php

namespace Mindee\Product\Resume;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The list of the candidate's professional experiences.
 */
class ResumeV1ProfessionalExperience
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string The type of contract for the professional experience.
     */
    public ?string $contractType;
    /**
     * @var string The specific department or division within the company.
     */
    public ?string $department;
    /**
     * @var string The name of the company or organization.
     */
    public ?string $employer;
    /**
     * @var string The month when the professional experience ended.
     */
    public ?string $endMonth;
    /**
     * @var string The year when the professional experience ended.
     */
    public ?string $endYear;
    /**
     * @var string The position or job title held by the candidate.
     */
    public ?string $role;
    /**
     * @var string The month when the professional experience began.
     */
    public ?string $startMonth;
    /**
     * @var string The year when the professional experience began.
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
        $this->contractType = $rawPrediction["contract_type"] ?? null;
        $this->department = $rawPrediction["department"] ?? null;
        $this->employer = $rawPrediction["employer"] ?? null;
        $this->endMonth = $rawPrediction["end_month"] ?? null;
        $this->endYear = $rawPrediction["end_year"] ?? null;
        $this->role = $rawPrediction["role"] ?? null;
        $this->startMonth = $rawPrediction["start_month"] ?? null;
        $this->startYear = $rawPrediction["start_year"] ?? null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["contractType"] = SummaryHelper::formatForDisplay($this->contractType, 15);
        $outArr["department"] = SummaryHelper::formatForDisplay($this->department, 10);
        $outArr["employer"] = SummaryHelper::formatForDisplay($this->employer, 25);
        $outArr["endMonth"] = SummaryHelper::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelper::formatForDisplay($this->endYear);
        $outArr["role"] = SummaryHelper::formatForDisplay($this->role, 20);
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
        $printable = $this->printableValues();
        $outStr = "| ";
        $outStr .= str_pad($printable["contractType"], 15) . " | ";
        $outStr .= str_pad($printable["department"], 10) . " | ";
        $outStr .= str_pad($printable["employer"], 25) . " | ";
        $outStr .= str_pad($printable["endMonth"], 9) . " | ";
        $outStr .= str_pad($printable["endYear"], 8) . " | ";
        $outStr .= str_pad($printable["role"], 20) . " | ";
        $outStr .= str_pad($printable["startMonth"], 11) . " | ";
        $outStr .= str_pad($printable["startYear"], 10) . " | ";
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
