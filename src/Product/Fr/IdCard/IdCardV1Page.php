<?php

namespace Mindee\Product\Fr\IdCard;

use Mindee\Parsing\Standard\ClassificationField;

/**
 * Page data for Carte Nationale d'Identité, API version 1.
 */
class IdCardV1Page extends IdCardV1Document
{
    /**
     * @var \Mindee\Parsing\Standard\ClassificationField The side of the document which is visible.
     */
    public ClassificationField $documentSide;

    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction);
        $this->documentSide = new ClassificationField($rawPrediction, $pageId);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = ":Document Side: $this->documentSide\n";
        $outStr .= parent::__toString();
        return trim($outStr);
    }
}
