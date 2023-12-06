<?php

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Custom\ListField;

/**
 * Custom V1 page prediction results.
 */
class CustomV1Page extends CustomV1Document
{
    /**
     * @var array Dictionary of all fields in the document.
     */
    public array $fields;

    /**
     * @param array        $rawPrediction Dictionary containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction, $pageId);
        $this->fields = [];
        foreach ($rawPrediction as $fieldName => $fieldContents) {
            $this->fields[$fieldName] = new ListField($fieldContents);
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = "";
        foreach ($this->fields as $fieldName => $fieldValue) {
            $outStr .= ":$fieldName: $fieldValue\n";
        }
        return SummaryHelper::cleanOutString($outStr);
    }
}
