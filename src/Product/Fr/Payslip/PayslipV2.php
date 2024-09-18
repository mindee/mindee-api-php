<?php

/** Payslip V2. */

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Payslip API version 2 inference prediction.
 */
class PayslipV2 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "payslip_fra";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "2";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new PayslipV2Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(PayslipV2Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
