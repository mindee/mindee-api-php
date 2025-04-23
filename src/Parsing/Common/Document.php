<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\ErrorCode;
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
        } catch (\ReflectionException $e) {
            throw new MindeeApiException(
                "Unable to create custom product " . $predictionType,
                ErrorCode::INTERNAL_LIBRARY_ERROR,
                $e
            );
        }
        if (array_key_exists('ocr', $rawResponse) && $rawResponse['ocr']) {
            $this->ocr = new Ocr($rawResponse['ocr']);
        }
        if (array_key_exists("extras", $rawResponse['inference']) && $rawResponse['inference']['extras']) {
            $this->extras = new Extras($rawResponse['inference']['extras']);
        }
        $this->injectFullTextOcr($rawResponse);
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

    /**
     * Injects the results from pages' "full_text_ocr", if present.
     *
     * @param array $rawResponse Raw HTTP response.
     * @return void
     */
    private function injectFullTextOcr(array $rawResponse): void
    {
        $pages = $rawResponse['inference']['pages'] ?? [];

        if (
            empty($pages) ||
            !isset($pages[0]['extras']) ||
            !isset($pages[0]['extras']['full_text_ocr'])
        ) {
            return;
        }

        $fullTextContent = implode("\n", array_map(
            function ($page) {
                return $page['extras']['full_text_ocr']['content'] ?? '';
            },
            array_filter($pages, function ($page) {
                return isset($page['extras']['full_text_ocr']);
            })
        ));

        $artificialTextObj = ['content' => $fullTextContent];

        if (!isset($this->extras)) {
            $this->extras = new Extras(['full_text_ocr' => $artificialTextObj]);
        } else {
            $this->extras->addArtificialExtra(['full_text_ocr' => $artificialTextObj]);
        }
    }
}
