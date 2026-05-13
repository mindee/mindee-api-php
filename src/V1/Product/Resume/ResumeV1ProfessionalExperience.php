<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The list of the candidate's professional experiences.
 */
class ResumeV1ProfessionalExperience
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @var integer|null Page ID.
     */
    public ?int $pageId;

    /**
     * @param array<string, mixed> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->pageId = $pageId;
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
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["contractType"] = SummaryHelperV1::formatForDisplay($this->contractType, 15);
        $outArr["department"] = SummaryHelperV1::formatForDisplay($this->department, 10);
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description, 36);
        $outArr["employer"] = SummaryHelperV1::formatForDisplay($this->employer, 25);
        $outArr["endMonth"] = SummaryHelperV1::formatForDisplay($this->endMonth);
        $outArr["endYear"] = SummaryHelperV1::formatForDisplay($this->endYear);
        $outArr["role"] = SummaryHelperV1::formatForDisplay($this->role, 20);
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
        $outStr .= SummaryHelperV1::padString($printable["contractType"], 15);
        $outStr .= SummaryHelperV1::padString($printable["department"], 10);
        $outStr .= SummaryHelperV1::padString($printable["description"], 36);
        $outStr .= SummaryHelperV1::padString($printable["employer"], 25);
        $outStr .= SummaryHelperV1::padString($printable["endMonth"], 9);
        $outStr .= SummaryHelperV1::padString($printable["endYear"], 8);
        $outStr .= SummaryHelperV1::padString($printable["role"], 20);
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
