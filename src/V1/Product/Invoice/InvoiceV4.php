<?php

declare(strict_types=1);

/** Invoice V4. */

namespace Mindee\V1\Product\Invoice;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Invoice API version 4 inference prediction.
 */
class InvoiceV4 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "invoices";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "4";

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new InvoiceV4Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(InvoiceV4Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
