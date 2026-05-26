<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The list of certificates obtained by the candidate.
 */
class ResumeV1Certificate
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The grade obtained for the certificate.
     */
    public ?string $grade;
    /**
     * @var string|null The name of certification.
     */
    public ?string $name;
    /**
     * @var string|null The organization or institution that issued the certificate.
     */
    public ?string $provider;
    /**
     * @var string|null The year when a certificate was issued or received.
     */
    public ?string $year;
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
        $this->grade = $rawPrediction["grade"] ?? null;
        $this->name = $rawPrediction["name"] ?? null;
        $this->provider = $rawPrediction["provider"] ?? null;
        $this->year = $rawPrediction["year"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["grade"] = SummaryHelperV1::formatForDisplay($this->grade, 10);
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name, 30);
        $outArr["provider"] = SummaryHelperV1::formatForDisplay($this->provider, 25);
        $outArr["year"] = SummaryHelperV1::formatForDisplay($this->year);
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
        $outStr .= SummaryHelperV1::padString($printable["grade"], 10);
        $outStr .= SummaryHelperV1::padString($printable["name"], 30);
        $outStr .= SummaryHelperV1::padString($printable["provider"], 25);
        $outStr .= SummaryHelperV1::padString($printable["year"], 4);
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
