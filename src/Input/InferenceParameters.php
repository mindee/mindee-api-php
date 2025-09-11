<?php

namespace Mindee\Input;

/**
 * Parameters accepted by the asynchronous **inference** v2 endpoint.
 */
class InferenceParameters
{
    /**
     * @var string Model ID.
     */
    public string $modelId;

    /**
     * @var boolean|null Enhance extraction accuracy with Retrieval-Augmented Generation..
     */
    public ?bool $rag;

    /**
     * @var boolean|null Extract the full text content from the document as strings.
     */
    public ?bool $rawText;

    /**
     * @var boolean|null Calculate bounding box polygons for all fields.
     */
    public ?bool $polygon;

    /**
     * @var boolean|null Boost the precision and accuracy of all extractions.
     *      Calculate confidence scores for all fields.
     */
    public ?bool $confidence;

    /**
     * @var string|null Optional file alias.
     */
    public ?string $alias;

    /**
     * @var array<string> Optional webhook IDs.
     */
    public array $webhooksIds;

    /**
     * @var PollingOptions Polling options.
     */
    public PollingOptions $pollingOptions;

    /**
     * @param string              $modelId        ID of the model.
     * @param boolean|null        $rag            Whether to enable Retrieval-Augmented Generation.
     * @param boolean|null        $rawText        Whether to extract the full text content from the document as strings.
     * @param boolean|null        $polygon        Whether to calculate bounding box polygons for all fields.
     * @param boolean|null        $confidence     Whether to calculate confidence scores for all fields.
     * @param string|null         $alias          Optional file alias.
     * @param array<string>|null  $webhooksIds    List of webhook IDs.
     * @param PollingOptions|null $pollingOptions Polling options.
     */
    public function __construct(
        string $modelId,
        ?bool $rag = null,
        ?bool $rawText = null,
        ?bool $polygon = null,
        ?bool $confidence = null,
        ?string $alias = null,
        ?array $webhooksIds = null,
        ?PollingOptions $pollingOptions = null,
    ) {
        $this->modelId = $modelId;
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }
        $this->pollingOptions = $pollingOptions;
        $this->rag = $rag;
        $this->rawText = $rawText;
        $this->polygon = $polygon;
        $this->confidence = $confidence;

        if (isset($alias)) {
            $this->alias = $alias;
        }
        if (isset($webhooksIds)) {
            $this->webhooksIds = $webhooksIds;
        } else {
            $this->webhooksIds = [];
        }
    }
}
