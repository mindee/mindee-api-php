<?php

namespace Mindee\Parsing\Common\Ocr;

class Ocr
{
    public MVisionV1 $mvision_v1;

    public function __construct(array $raw_prediction)
    {
        $this->mvision_v1 = new MVisionV1($raw_prediction['mvision-v1']);
    }

    public function __toString(): string
    {
        return strval($this->mvision_v1);
    }
}
