<?php

namespace Mindee\Product\Resume;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The list of languages that the candidate is proficient in.
 */
class ResumeV1Language
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string The language's ISO 639 code.
     */
    public ?string $language;
    /**
     * @var string The candidate's level for the language.
     */
    public ?string $level;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->language = $rawPrediction["language"] ?? null;
        $this->level = $rawPrediction["level"] ?? null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["language"] = SummaryHelper::formatForDisplay($this->language);
        $outArr["level"] = SummaryHelper::formatForDisplay($this->level, 20);
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
        $outStr .= str_pad($printable["language"], 8) . " | ";
        $outStr .= str_pad($printable["level"], 20) . " | ";
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
