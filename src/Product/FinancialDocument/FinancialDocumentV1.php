<?php

/** Financial Document V1. */

namespace Mindee\Product\FinancialDocument;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Financial Document API version 1 inference prediction.
 */
class FinancialDocumentV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "financial_document";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "1";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new FinancialDocumentV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(FinancialDocumentV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
