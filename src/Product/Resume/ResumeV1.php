<?php

/** Resume V1. */

namespace Mindee\Product\Resume;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Resume API version 1 inference prediction.
 */
class ResumeV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "resume";
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
        $this->prediction = new ResumeV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(ResumeV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
