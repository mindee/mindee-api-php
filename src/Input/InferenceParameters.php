<?php

namespace Mindee\Input;

use Mindee\V2\ClientOptions\BaseParameters;

/**
 * Parameters accepted by the asynchronous **inference** v2 endpoint.
 */
class InferenceParameters extends BaseParameters
{
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
     * @var string|null Additional text context used by the model during inference.
     * Not recommended, for specific use only.
     */
    public ?string $textContext;

    /**
     * @var DataSchema|null Data schema for inference.
     */
    public ?DataSchema $dataSchema;

    /**
     * @var string Slug of the endpoint.
     */
    public static string $slug = "extraction";

    /**
     * @param string                       $modelId        ID of the model.
     * @param boolean|null                 $rag            Whether to enable Retrieval-Augmented Generation.
     * @param boolean|null                 $rawText        Whether to extract the full text content from the
     * document as strings.
     * @param boolean|null                 $polygon        Whether to calculate bounding box polygons for all
     * fields.
     * @param boolean|null                 $confidence     Whether to calculate confidence scores for all fields.
     * @param string|null                  $alias          Optional file alias.
     * @param array<string>|null           $webhooksIds    List of webhook IDs.
     * @param string|null                  $textContext    Additional text context used by the model during
     * inference.
     * @param DataSchema|string|array|null $dataSchema     Additional text context used by the model during
     * inference.
     * @param PollingOptions|null          $pollingOptions Polling options.
     */
    public function __construct(
        string $modelId,
        ?bool $rag = null,
        ?bool $rawText = null,
        ?bool $polygon = null,
        ?bool $confidence = null,
        ?string $alias = null,
        ?array $webhooksIds = null,
        ?string $textContext = null,
        DataSchema|string|array|null $dataSchema = null,
        ?PollingOptions $pollingOptions = null,
    ) {
        parent::__construct($modelId, $alias, $webhooksIds, $pollingOptions);

        $this->rag = $rag;
        $this->rawText = $rawText;
        $this->polygon = $polygon;
        $this->confidence = $confidence;
        if (isset($textContext)) {
            $this->textContext = $textContext;
        }
        if (isset($dataSchema)) {
            $this->dataSchema = new DataSchema($dataSchema);
        }
    }

    /**
     * @return array Hash representation.
     */
    public function asHash(): array
    {
        $outHash = parent::asHash();
        if (isset($this->rag)) {
            $outHash['rag'] = $this->rag ? 'true' : 'false';
        }
        if (isset($this->rawText)) {
            $outHash['raw_text'] = $this->rawText ? 'true' : 'false';
        }
        if (isset($this->polygon)) {
            $outHash['polygon'] = $this->polygon ? 'true' : 'false';
        }
        if (isset($this->confidence)) {
            $outHash['confidence'] = $this->confidence ? 'true' : 'false';
        }
        if (isset($this->textContext)) {
            $outHash['text_context'] = $this->textContext;
        }
        if (isset($this->dataSchema)) {
            $outHash['data_schema'] = strval($this->dataSchema);
        }
        return $outHash;
    }
}
