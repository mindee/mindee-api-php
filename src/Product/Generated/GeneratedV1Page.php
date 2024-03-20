<?php

namespace Mindee\Product\Generated;

use Mindee\Parsing\Generated\GeneratedListField;
use Mindee\Parsing\Generated\GeneratedObjectField;
use Mindee\Parsing\Standard\StringField;

/**
 * Generated V1 page prediction results.
 */
class GeneratedV1Page extends GeneratedV1Prediction
{
    /** @var array Dictionary of all fields in the document */
    public array $fields;

    /**
     * GeneratedV1Page constructor.
     * @param array        $rawPrediction Dictionary containing the JSON document response.
     * @param integer|null $pageId        ID of the page.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction);
        $this->fields = [];
        foreach ($rawPrediction as $fieldName => $fieldContents) {
            if (is_array($fieldContents) && array_values($fieldContents) === $fieldContents) {
                $this->fields[$fieldName] = new GeneratedListField($fieldContents, $pageId);
            } elseif (is_array($fieldContents) && GeneratedObjectField::isGeneratedObject($fieldContents)) {
                $this->fields[$fieldName] = new GeneratedObjectField($fieldContents, $pageId);
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
                $this->fields[$fieldName] = new StringField($fieldContentsStr, $pageId);
            }
        }
    }
}
