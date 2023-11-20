<?php

namespace Mindee\Product\Fr\IdCard;

use Mindee\Parsing\Standard\ClassificationField;

class IdCardV1Page extends IdCardV1Document
{
    public ClassificationField $documentSide;

    function __construct(array $raw_prediction, ?int $page_id = null)
    {
        parent::__construct($raw_prediction);
        $this->documentSide = new ClassificationField($raw_prediction, $page_id);
    }

    public function __toString(): string
    {
        $out_str = ":Document Side: $this->documentSide\n";
        $out_str .= parent::__toString();
        return trim($out_str);
    }
}
