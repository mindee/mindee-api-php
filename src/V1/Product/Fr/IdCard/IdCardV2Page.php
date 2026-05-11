<?php

namespace Mindee\V1\Product\Fr\IdCard;

use Mindee\V1\Parsing\Standard\ClassificationField;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Carte Nationale d'Identité API version 2.0 page data.
 */
class IdCardV2Page extends IdCardV2Document
{
    /**
     * @var ClassificationField The sides of the document which are visible.
     */
    public ClassificationField $documentSide;
    /**
     * @var ClassificationField The document type or format.
     */
    public ClassificationField $documentType;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction, $pageId);
        $this->documentSide = new ClassificationField(
            $rawPrediction["document_side"],
            $pageId
        );
        $this->documentType = new ClassificationField(
            $rawPrediction["document_type"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Document Type: $this->documentType
:Document Sides: $this->documentSide
";
        $outStr .= parent::__toString();
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
