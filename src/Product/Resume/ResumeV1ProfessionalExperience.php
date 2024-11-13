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
     * @var string|null The type of contract for the professional experience.
     */
    public ?string $contractType;
    /**
     * @var string|null The specific department or division within the company.
     */
    public ?string $department;
    /**
     * @var string|null The description of the professional experience as written in the document.
     */
    public ?string $description;
    /**
     * @var string|null The name of the company or organization.
     */
    public ?string $employer;
    /**
     * @var string|null The month when the professional experience ended.
     */
    public ?string $endMonth;
    /**
     * @var string|null The year when the professional experience ended.
     */
    public ?string $endYear;
    /**
     * @var string|null The position or job title held by the candidate.
     */
    public ?string $role;
    /**
     * @var string|null The month when the professional experience began.
     */
    public ?string $startMonth;
    /**
     * @var string|null The year when the professional experience began.
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
        $this->description = $rawPrediction["description"] ?? null;
        $this->employer = $rawPrediction["employer"] ?? null;
        $this->endMonth = $rawPrediction["end_month"] ?? null;
        $this->endYear = $rawPrediction["end_year"] ?? null;
        $this->role = $rawPrediction["role"] ?? null;
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
        $outArr["contractType"] = SummaryHelper::formatForDisplay($this->contractType, 15);
        $outArr["department"] = SummaryHelper::formatForDisplay($this->department, 10);
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description, 36);
        $outArr["employer"] = SummaryHelper::formatForDisplay($this->employer, 25);
        $outArr["endMonth"] = SummaryHelper::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelper::formatForDisplay($this->endYear);
        $outArr["role"] = SummaryHelper::formatForDisplay($this->role, 20);
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
        $outArr["contractType"] = SummaryHelper::formatForDisplay($this->contractType);
        $outArr["department"] = SummaryHelper::formatForDisplay($this->department);
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description);
        $outArr["employer"] = SummaryHelper::formatForDisplay($this->employer);
        $outArr["endMonth"] = SummaryHelper::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelper::formatForDisplay($this->endYear);
        $outArr["role"] = SummaryHelper::formatForDisplay($this->role);
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
        $outStr .= SummaryHelper::padString($printable["contractType"], 15);
        $outStr .= SummaryHelper::padString($printable["department"], 10);
        $outStr .= SummaryHelper::padString($printable["description"], 36);
        $outStr .= SummaryHelper::padString($printable["employer"], 25);
        $outStr .= SummaryHelper::padString($printable["endMonth"], 9);
        $outStr .= SummaryHelper::padString($printable["endYear"], 8);
        $outStr .= SummaryHelper::padString($printable["role"], 20);
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
