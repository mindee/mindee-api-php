<?php

namespace Mindee\Product\Resume;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The list of certificates obtained by the candidate.
 */
class ResumeV1Certificate
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string The grade obtained for the certificate.
     */
    public ?string $grade;
    /**
     * @var string The name of certification.
     */
    public ?string $name;
    /**
     * @var string The organization or institution that issued the certificate.
     */
    public ?string $provider;
    /**
     * @var string The year when a certificate was issued or received.
     */
    public ?string $year;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->grade = $rawPrediction["grade"] ?? null;
        $this->name = $rawPrediction["name"] ?? null;
        $this->provider = $rawPrediction["provider"] ?? null;
        $this->year = $rawPrediction["year"] ?? null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["grade"] = SummaryHelper::formatForDisplay($this->grade, 10);
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name, 30);
        $outArr["provider"] = SummaryHelper::formatForDisplay($this->provider, 25);
        $outArr["year"] = SummaryHelper::formatForDisplay($this->year);
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
        $outStr .= str_pad($printable["grade"], 10) . " | ";
        $outStr .= str_pad($printable["name"], 30) . " | ";
        $outStr .= str_pad($printable["provider"], 25) . " | ";
        $outStr .= str_pad($printable["year"], 4) . " | ";
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
