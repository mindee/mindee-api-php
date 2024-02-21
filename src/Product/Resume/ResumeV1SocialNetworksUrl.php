<?php

namespace Mindee\Product\Resume;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The list of social network profiles of the candidate.
 */
class ResumeV1SocialNetworksUrl
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string The name of the social network.
     */
    public ?string $name;
    /**
     * @var string The URL of the social network.
     */
    public ?string $url;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->name = $rawPrediction["name"] ?? null;
        $this->url = $rawPrediction["url"] ?? null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name, 20);
        $outArr["url"] = SummaryHelper::formatForDisplay($this->url, 50);
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
        $outStr .= str_pad($printable["name"], 20) . " | ";
        $outStr .= str_pad($printable["url"], 50) . " | ";
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
