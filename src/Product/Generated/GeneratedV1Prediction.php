<?php

/**
 * Generated V1.
 */

namespace Mindee\Product\Generated;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Generated\GeneratedListField;
use Mindee\Parsing\Standard\StringField;
use Mindee\Parsing\Generated\GeneratedObjectField;

/**
 * Generated V1 document prediction results.
 */
class GeneratedV1Prediction extends Prediction
{
    /** @var array<string, GeneratedListField|StringField|GeneratedObjectField> */
    public array $fields = [];

    /**
     * GeneratedV1Prediction constructor.
     * @param array $rawPrediction Dictionary containing the JSON document response.
     */
    public function __construct(array $rawPrediction)
    {
        $this->fields = [];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = "";
        $pattern = "/^(\n*[  ]*)( {2}):/";
        foreach ($this->fields as $fieldName => $fieldValue) {
            $strValue = "";
            if ($fieldValue instanceof GeneratedListField && count($fieldValue->values) > 0) {
                if ($fieldValue->values[0] instanceof GeneratedObjectField) {
                    $strValue .= preg_replace($pattern, "\\1* :", "{$fieldValue->values[0]->strLevel(1)}");
                } else {
                    $strValue .= preg_replace($pattern, "\\1* :", "{$fieldValue->values[0]}\n");
                }
                for ($i = 1; $i < count($fieldValue->values); $i++) {
                    if ($fieldValue->values[$i] instanceof GeneratedObjectField) {
                        $strValue .= preg_replace($pattern, "\\1* :", "{$fieldValue->values[$i]->strLevel(1)}");
                    } else {
                        $strValue .= " " . str_repeat(" ", strlen($fieldName) + 2) . "{$fieldValue->values[$i]}\n";
                    }
                }
                $strValue = rtrim($strValue);
            } else {
                $strValue = strval($fieldValue);
            }
            $outStr .= ":{$fieldName}: {$strValue}\n";
        }
        return SummaryHelper::cleanOutString($outStr);
    }

    /**
     * Returns a dictionary of all fields that aren't a collection.
     * @return array<string, StringField>
     */
    public function getSingleFields(): array
    {
        $singleFields = [];
        foreach ($this->fields as $fieldName => $fieldValue) {
            if ($fieldValue instanceof StringField) {
                $singleFields[$fieldName] = $fieldValue;
            }
        }
        return $singleFields;
    }

    /**
     * Returns a dictionary of all list-like fields.
     * @return array<string, GeneratedListField>
     */
    public function getListFields(): array
    {
        $listFields = [];
        foreach ($this->fields as $fieldName => $fieldValue) {
            if ($fieldValue instanceof GeneratedListField) {
                $listFields[$fieldName] = $fieldValue;
            }
        }
        return $listFields;
    }

    /**
     * Returns a dictionary of all object-like fields.
     * @return array<string, GeneratedObjectField>
     */
    public function getObjectFields(): array
    {
        $objectFields = [];
        foreach ($this->fields as $fieldName => $fieldValue) {
            if ($fieldValue instanceof GeneratedObjectField) {
                $objectFields[$fieldName] = $fieldValue;
            }
        }
        return $objectFields;
    }

    /**
     * Lists names of all top-level field keys.
     * @return array<string>
     */
    public function listFieldNames(): array
    {
        return array_keys($this->fields);
    }
}
