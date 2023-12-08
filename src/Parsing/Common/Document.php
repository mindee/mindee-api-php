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
    public int $nPages;
    /**
     * @var \Mindee\Parsing\Common\Extras\Extras|null Potential Extras fields sent back along with the prediction.
     */
    public ?Extras $extras;
    /**
     * @var \Mindee\Parsing\Common\Ocr\Ocr|null Potential raw text results read by the OCR (limited feature)
     */
    public ?Ocr $ocr;

    /**
     * @param string $predictionType Type of prediction.
     * @param array  $rawResponse    Raw HTTP response.
     * @throws \Mindee\Error\MindeeApiException Throws if the prediction type isn't recognized.
     */
    public function __construct(string $predictionType, array $rawResponse)
    {
        $this->id = $rawResponse['id'];
        $this->nPages = $rawResponse['n_pages'];
        $this->filename = $rawResponse['name'];
        try {
            $reflection = new \ReflectionClass($predictionType);
            $this->inference = $reflection->newInstance($rawResponse['inference']);
        } catch (\ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $predictionType);
        }
        if (array_key_exists("ocr", $rawResponse) && $rawResponse['ocr']) {
            $this->ocr = new Ocr($rawResponse['ocr']);
        }
        if (array_key_exists("extras", $rawResponse) && $rawResponse['extras']) {
            $this->extras = new Extras($rawResponse['extras']);
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
:Mindee ID: $this->id
:Filename: $this->filename

$this->inference";
    }
}
