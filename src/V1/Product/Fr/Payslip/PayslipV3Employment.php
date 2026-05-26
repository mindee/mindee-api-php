<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;
use Stringable;

/**
 * Information about the employment.
 */
class PayslipV3Employment implements Stringable
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The category of the employment.
     */
    public ?string $category;
    /**
     * @var string|null The coefficient of the employment.
     */
    public ?string $coefficient;
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
     * @var string|null The seniority date of the employment.
     */
    public ?string $seniorityDate;
    /**
     * @var string|null The start date of the employment.
     */
    public ?string $startDate;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, public ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->category = $rawPrediction["category"] ?? null;
        $this->coefficient = $rawPrediction["coefficient"] ?? null;
        $this->collectiveAgreement = $rawPrediction["collective_agreement"] ?? null;
        $this->jobTitle = $rawPrediction["job_title"] ?? null;
        $this->positionLevel = $rawPrediction["position_level"] ?? null;
        $this->seniorityDate = $rawPrediction["seniority_date"] ?? null;
        $this->startDate = $rawPrediction["start_date"] ?? null;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["category"] = SummaryHelperV1::formatForDisplay($this->category);
        $outArr["coefficient"] = SummaryHelperV1::formatForDisplay($this->coefficient);
        $outArr["collectiveAgreement"] = SummaryHelperV1::formatForDisplay($this->collectiveAgreement);
        $outArr["jobTitle"] = SummaryHelperV1::formatForDisplay($this->jobTitle);
        $outArr["positionLevel"] = SummaryHelperV1::formatForDisplay($this->positionLevel);
        $outArr["seniorityDate"] = SummaryHelperV1::formatForDisplay($this->seniorityDate);
        $outArr["startDate"] = SummaryHelperV1::formatForDisplay($this->startDate);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
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
        $outStr .= "\n  :Seniority Date: " . $printable["seniorityDate"];
        $outStr .= "\n  :Start Date: " . $printable["startDate"];
        return rtrim($outStr);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
