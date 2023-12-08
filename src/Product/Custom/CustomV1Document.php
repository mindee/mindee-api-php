<?php

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Custom\ClassificationField;
use Mindee\Parsing\Custom\CustomLine;
use Mindee\Parsing\Custom\ListField;

/**
 * Custom V1 document prediction results.
 */
class CustomV1Document extends Prediction
{
    /**
     * @var array Array of all fields in the document.
     */
    public array $fields;
    /**
     * @var array Array of all classifications in the document.
     */
    public array $classifications;

    /**
     * @param array $rawPrediction Array containing the JSON document response.
     */
    public function __construct(array $rawPrediction)
    {
        $this->fields = [];
        $this->classifications = [];

        foreach ($rawPrediction as $fieldName => $fieldContents) {
            if (array_key_exists("value", $fieldContents)) {
                $this->classifications[$fieldName] = new ClassificationField($fieldContents);
            } elseif (array_key_exists("values", $fieldContents)) {
                $this->fields[$fieldName] = new ListField($fieldContents);
            }
        }
    }

    /**
     * Order column fields into line items.
     *
     * @param array $anchorNames     List of possible anchor fields.
     * @param array $fieldNames      List of all column fields.
     * @param float $heightTolerance Height tolerance to apply to lines.
     * @return array
     */
    public function columnsToLineItems(array $anchorNames, array $fieldNames, float $heightTolerance): array
    {
        return CustomLine::getLineItems(
            $anchorNames,
            $fieldNames,
            $this->fields,
            $heightTolerance
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = "";
        foreach ($this->classifications as $classificationName => $classificationValue) {
            $outStr .= ":$classificationName: $classificationValue\n";
        }
        foreach ($this->fields as $fieldName => $fieldValue) {
            $outStr .= ":$fieldName: $fieldValue\n";
        }
        return SummaryHelper::cleanOutString($outStr);
    }
}
