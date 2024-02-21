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
            if (is_array($fieldContents) && array_keys($fieldContents) === range(0, count($fieldContents) - 1)) {
                $this->fields[$fieldName] = new GeneratedListField($fieldContents, $pageId);
            } elseif (is_array($fieldContents) && GeneratedObjectField::isGeneratedObject($fieldContents)) {
                $this->fields[$fieldName] = new GeneratedObjectField($fieldContents, $pageId);
            } else {
                $fieldContentsStr = $fieldContents;
                if (isset($fieldContentsStr['value'])) {
                    $fieldContentsStr['value'] = strval($fieldContentsStr['value']);
                }
                $this->fields[$fieldName] = new StringField($fieldContentsStr, $pageId);
            }
        }
    }
}
