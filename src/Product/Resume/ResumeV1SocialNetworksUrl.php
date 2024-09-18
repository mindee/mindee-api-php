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
     * @var string|null The name of the social network.
     */
    public ?string $name;
    /**
     * @var string|null The URL of the social network.
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
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name, 20);
        $outArr["url"] = SummaryHelper::formatForDisplay($this->url, 50);
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
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name);
        $outArr["url"] = SummaryHelper::formatForDisplay($this->url);
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
        $outStr .= SummaryHelper::padString($printable["name"], 20);
        $outStr .= SummaryHelper::padString($printable["url"], 50);
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
