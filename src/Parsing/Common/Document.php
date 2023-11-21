<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;
use Mindee\Parsing\Common\Extras\Extras;
use Mindee\Parsing\Common\Ocr\Ocr;

class Document
{
    public string $filename;
    public Inference $inference;
    public string $id;
    public int $n_pages;
    public ?Extras $extras;
    public ?Ocr $ocr;

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
        if (array_key_exists("ocr", $raw_response) && $raw_response['ocr']) {
            $this->ocr = new Ocr($raw_response['ocr']);
        }
        if (array_key_exists("extras", $raw_response) && $raw_response['extras']) {
            $this->extras = new Extras($raw_response['extras']);
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
