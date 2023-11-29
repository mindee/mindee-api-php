<?php

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Custom\ListField;

/**
 * Custom V1 page prediction results.
 */
class CustomV1Page extends Prediction
{
    /**
     * @var array Dictionary of all fields in the document.
     */
    public array $fields;

    /**
     * @param array $rawPrediction Dictionary containing the JSON document response.
     */
    public function __construct(array $rawPrediction)
    {
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
        return trim($outStr);
    }
}
