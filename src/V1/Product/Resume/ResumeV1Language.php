<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The list of languages that the candidate is proficient in.
 */
class ResumeV1Language
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The language's ISO 639 code.
     */
    public ?string $language;
    /**
     * @var string|null The candidate's level for the language.
     */
    public ?string $level;
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
        $this->language = $rawPrediction["language"] ?? null;
        $this->level = $rawPrediction["level"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["language"] = SummaryHelperV1::formatForDisplay($this->language);
        $outArr["level"] = SummaryHelperV1::formatForDisplay($this->level, 20);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["language"] = SummaryHelperV1::formatForDisplay($this->language);
        $outArr["level"] = SummaryHelperV1::formatForDisplay($this->level);
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
        $outStr .= SummaryHelperV1::padString($printable["language"], 8);
        $outStr .= SummaryHelperV1::padString($printable["level"], 20);
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
