<?php

namespace Mindee;

use Mindee\http\Endpoint;
use Mindee\input\PageOptions;
use Mindee\parsing\common\Inference;
use Mindee\parsing\common\PredictReponse;

const VERSION = '1.0.0';
const DEFAULT_OWNER = 'mindee';
/**
 * Main entrypoint for Mindee operations.
 */
class Client
{
    /**
     * @var string api key for a given client
     */
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    /**
     * @param string $file_path path of the file
     */
    public function sourceFromPath(string $file_path): \CURLFile
    {
        $file_name = basename($file_path);
        $mime_type = mime_content_type($file_path);

        return new \CURLFile($file_path, $mime_type, $file_name);
    }

    public function sourceFromBytes(string $file_bytes, string $file_name): \CURLFile
    {
        $file_b64 = 'data://application/pdf;base64,'.base64_encode($file_bytes);
        $file = finfo_open();
        $mime_type = finfo_buffer($file, base64_decode($file_b64), FILEINFO_MIME_TYPE);

        return new \CURLFile($file_b64, $mime_type, $file_name);
    }

    public function sourceFromb64String(string $file_b64, string $file_name): \CURLFile
    {
        $file = finfo_open();
        $mime_type = finfo_buffer($file, base64_decode($file_b64), FILEINFO_MIME_TYPE);

        return new \CURLFile($file_b64, $mime_type, $file_name);
    }

    public function sourceFromUrl(string $file_b64, string $file_name): \CURLFile
    {
        $file = finfo_open();
        $mime_type = finfo_buffer($file, base64_decode($file_b64), FILEINFO_MIME_TYPE);

        return new \CURLFile($file_b64, $mime_type, $file_name);
    }

    private function constructEndpoint(string $endpoint_name, string $endpoint_owner, string $endpoint_version): Endpoint
    {
        $endpoint_version = $endpoint_version != null && strlen($endpoint_version) > 0 ? $endpoint_version : '1';

        return new Endpoint($this->apiKey, $endpoint_name, $endpoint_owner, $endpoint_version);
    }

    private function constructOTSEndpoint($product): Endpoint
    {
        if ($product->endpoint_name == 'custom') {
            throw new \InvalidArgumentException('Please create an endpoint manually before sending requests to a custom build.');
        }
        $endpoint_owner = DEFAULT_OWNER;

        return $this->constructEndpoint($product->endpoint_name, $endpoint_owner, $product->endpoint_version);
    }

    private function makeParseRequest(
        Inference $prediction_type,
        \CURLFile $input_doc,
        Endpoint $endpoint,
        bool $include_words,
        bool $close_file,
        bool $cropper
    ): PredictReponse {
        $response = $endpoint->predictRequestPost($input_doc, $include_words, $close_file, $cropper);
        if (!$response['ok']) {
            throw new \ErrorException('Response not ok.'); // TODO: implement error handling module.
        }

        return new PredictReponse($prediction_type, $response);
    }

    public function parse(
        Inference $prediction_type,
        \CURLFile $input_doc,
        ?bool $include_words = false,
        ?bool $close_file = true,
        ?PageOptions $page_options = null,// TODO: PageOptions initialization
        ?bool $cropper = false,
        ?Endpoint $custom_endpoint = null
    ): PredictReponse {
        $endpoint = isset($custom_endpoint) ?
            $custom_endpoint :
            $this->constructOTSEndpoint(
                $prediction_type,
            );

        return $this->makeParseRequest($prediction_type, $input_doc, $endpoint, $include_words, $close_file, $cropper);
    }
}
