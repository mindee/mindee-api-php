<?php

namespace Mindee\parsing\common;

class PredictReponse extends ApiResponse
{
    public Document $document;

    public function __construct(string $prediction_type, array $raw_response)
    {
        parent::__construct($raw_response);
        $this->document = new Document($prediction_type, $raw_response['document']);
    }
}
