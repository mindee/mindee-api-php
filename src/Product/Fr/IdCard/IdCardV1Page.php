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
     * @param array        $raw_prediction Raw prediction from HTTP response.
     * @param integer|null $page_id        Page number for multi pages pdf input.
     */
    public function __construct(array $raw_prediction, ?int $page_id = null)
    {
        parent::__construct($raw_prediction);
        $this->documentSide = new ClassificationField($raw_prediction, $page_id);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $out_str = ":Document Side: $this->documentSide\n";
        $out_str .= parent::__toString();
        return trim($out_str);
    }
}
