<?php

/** Payslip V3. */

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Payslip API version 3 inference prediction.
 */
class PayslipV3 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "payslip_fra";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "3";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new PayslipV3Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(PayslipV3Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
