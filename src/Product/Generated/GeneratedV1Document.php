<?php

namespace Mindee\Product\Generated;

use Mindee\Parsing\Generated\GeneratedListField;
use Mindee\Parsing\Generated\GeneratedObjectField;
use Mindee\Parsing\Standard\StringField;

/**
 * Generated V1 document prediction results.
 */
class GeneratedV1Document extends GeneratedV1Prediction
{
    /** @var array Dictionary of all fields in the document. */
    public array $fields;

    /**
     * GeneratedV1Document constructor.
     * @param array $rawPrediction Dictionary containing the JSON document response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->fields = [];
        foreach ($rawPrediction as $fieldName => $fieldContents) {
            if (is_array($fieldContents) && array_values($fieldContents) === $fieldContents) {
                $this->fields[$fieldName] = new GeneratedListField($fieldContents);
            } elseif (is_array($fieldContents) && GeneratedObjectField::isGeneratedObject($fieldContents)) {
                $this->fields[$fieldName] = new GeneratedObjectField($fieldContents);
            } else {
                $fieldContentsStr = $fieldContents;
                if (isset($fieldContentsStr['value'])) {
                    if (
                        (is_int($fieldContentsStr['value']) ||
                            (is_float($fieldContentsStr['value']) &&
                                floor($fieldContentsStr['value']) == $fieldContentsStr['value'])) &&
                        $fieldContentsStr['value'] != 0.0
                    ) {
                        $this->{$fieldName} = $fieldContentsStr['value'] . ".0";
                    } else {
                        $fieldContentsStr['value'] = strval($fieldContents['value']);
                    }
                } else {
                    $fieldContentsStr['value'] = null;
                }
                $this->fields[$fieldName] = new StringField($fieldContentsStr);
            }
        }
    }
}
