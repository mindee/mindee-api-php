<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;

class Document
{
    public string $filename;
    public Inference $inference;
    public string $id;
    public int $n_pages;
    public $extras;
    public $ocr;

    public function __construct(string $prediction_type, array $raw_response)
    {
        $this->id = $raw_response['id'];
        $this->n_pages = $raw_response['n_pages'];
        $this->filename = $raw_response['name'];
        try {
            $reflection = new \ReflectionClass($prediction_type);
            $this->inference = $reflection->newInstance($raw_response['inference']);
        } catch (\ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $prediction_type);
        }
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
