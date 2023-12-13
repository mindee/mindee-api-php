<?php

/** Expense Receipt V5. */

namespace Mindee\Product\Receipt;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for Expense Receipt, API version 5.
 */
class ReceiptV5 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "expense_receipts";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "5";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new ReceiptV5Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $this->pages[] = new Page(ReceiptV5Document::class, $page);
        }
    }
}
