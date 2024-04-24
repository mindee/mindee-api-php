<?php

/** Proof of Address V1. */

namespace Mindee\Product\ProofOfAddress;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Proof of Address API version 1 inference prediction.
 */
class ProofOfAddressV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "proof_of_address";
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
        $this->prediction = new ProofOfAddressV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(ProofOfAddressV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
