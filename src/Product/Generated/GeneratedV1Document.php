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
    /** @var array Dictionary of all fields in the document */
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
            if (is_array($fieldContents) && array_keys($fieldContents) === range(0, count($fieldContents) - 1)) {
                $this->fields[$fieldName] = new GeneratedListField($fieldContents);
            } elseif (is_array($fieldContents) && GeneratedObjectField::isGeneratedObject($fieldContents)) {
                $this->fields[$fieldName] = new GeneratedObjectField($fieldContents);
            } else {
                $fieldContentsStr = $fieldContents;
                if (isset($fieldContentsStr['value'])) {
                    $fieldContentsStr['value'] = strval($fieldContentsStr['value']);
                }
                $this->fields[$fieldName] = new StringField($fieldContentsStr);
            }
        }
    }
}
