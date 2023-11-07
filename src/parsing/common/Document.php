<?php

namespace Mindee\parsing\common;

class Document
{
    public string $filename;
    public Inference $inference;
    public string $id;
    public int $n_pages;
    public $extras;
    public $ocr;

    public function __construct(Inference $prediction_type, array $raw_response)
    {
        $this->id = $raw_response['id'];
        $this->n_pages = $raw_response['n_pages'];
        $this->filename = $raw_response['name'];
        $this->inference = new $prediction_type($raw_response);
    }

    public function __toString(): string
    {
        return "########
Document
########
Mindee ID: $this->id
:Filename: $this->filename

$this->inference";
    }
}
