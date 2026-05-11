<?php

/** Receipt V5. */

namespace Mindee\V1\Product\Receipt;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Receipt API version 5 inference prediction.
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
            try {
                $this->pages[] = new Page(ReceiptV5Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
