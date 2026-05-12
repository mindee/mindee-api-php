<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\IdCard;

use Mindee\V1\Parsing\Standard\ClassificationField;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Carte Nationale d'Identité API version 1.1 page data.
 */
class IdCardV1Page extends IdCardV1Document
{
    /**
     * @var ClassificationField The side of the document which is visible.
     */
    public ClassificationField $documentSide;
    /**
     * @param array $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction, $pageId);
        $this->documentSide = new ClassificationField(
            $rawPrediction["document_side"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Document Side: $this->documentSide
";
        $outStr .= parent::__toString();
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
