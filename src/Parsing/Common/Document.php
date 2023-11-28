<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;
use Mindee\Parsing\Common\Extras\Extras;
use Mindee\Parsing\Common\Ocr\Ocr;

/**
 * Base class for all predictions.
 */
class Document
{
    /**
     * @var string|mixed Name of the input document.
     */
    public string $filename;
    /**
     * @var \Mindee\Parsing\Common\Inference|object|string Result of the base inference.
     */
    public Inference $inference;
    /**
     * @var string|mixed ID of the document as sent back by the server.
     */
    public string $id;
    /**
     * @var integer|mixed Amount of pages in the document
     */
    public int $n_pages;
    /**
     * @var \Mindee\Parsing\Common\Extras\Extras|null Potential Extras fields sent back along with the prediction.
     */
    public ?Extras $extras;
    /**
     * @var \Mindee\Parsing\Common\Ocr\Ocr|null Potential raw text results read by the OCR (limited feature)
     */
    public ?Ocr $ocr;

    /**
     * @param string $prediction_type Type of prediction.
     * @param array  $raw_response    Raw HTTP response.
     * @throws \Mindee\Error\MindeeApiException Throws if the prediction type isn't recognized.
     */
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

    /**
     * @return string String representation.
     */
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
