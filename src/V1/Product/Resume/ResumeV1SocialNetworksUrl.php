<?php

namespace Mindee\V1\Product\Resume;

use Mindee\V1\Parsing\Common\SummaryHelperV1;
use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;

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
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name, 20);
        $outArr["url"] = SummaryHelperV1::formatForDisplay($this->url, 50);
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
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name);
        $outArr["url"] = SummaryHelperV1::formatForDisplay($this->url);
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
        $outStr .= SummaryHelperV1::padString($printable["name"], 20);
        $outStr .= SummaryHelperV1::padString($printable["url"], 50);
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
