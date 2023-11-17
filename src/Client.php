<?php

namespace Mindee;

use Mindee\error\MindeeClientException;
use Mindee\input\InputSource;
use Mindee\input\PathInput;
use function Mindee\error\handle_error;
use Mindee\error\MindeeApiException;
use Mindee\http\Endpoint;
use Mindee\http\MindeeApi;
use Mindee\input\Base64Input;
use Mindee\input\BytesInput;
use Mindee\input\FileInput;
use Mindee\input\LocalInputSource;
use Mindee\input\PageOptions;
use Mindee\input\URLInputSource;
use Mindee\parsing\common\Inference;
use Mindee\parsing\common\PredictReponse;

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
    public function sourceFromPath(string $file_path): PathInput
    {

        return new PathInput($file_path);
    }

    public function sourceFromFile($file): LocalInputSource
    {
        return new FileInput($file);
    }

    public function sourceFromBytes(string $file_bytes, string $file_name): BytesInput
    {
        return new BytesInput($file_bytes, $file_name);
    }

    public function sourceFromb64String(string $file_b64, string $file_name): Base64Input
    {
        return new Base64Input($file_b64, $file_name);
    }

    public function sourceFromUrl(string $url): URLInputSource
    {
        return new URLInputSource($url);
    }

    private function constructEndpoint(string $endpoint_name, string $endpoint_owner, string $endpoint_version): Endpoint
    {
        $endpoint_version = $endpoint_version != null && strlen($endpoint_version) > 0 ? $endpoint_version : '1';

        $endpoint_settings = new MindeeApi($this->apiKey, $endpoint_name, $endpoint_owner, $endpoint_version);

        return new Endpoint($endpoint_name, $endpoint_owner, $endpoint_version, $endpoint_settings);
    }

    private function cleanAccountName(string $account_name):string{
        if (!$account_name || count(trim($account_name))<1){
            error_log("No account name provided for custom build. ".DEFAULT_OWNER." will be used by default.");
            return DEFAULT_OWNER;
        }
        return $account_name;
    }
    private function constructOTSEndpoint($product): Endpoint
    {
        if ($product->endpoint_name == 'custom') {
            throw new MindeeApiException('Please create an endpoint manually before sending requests to a custom build.');
        }
        $endpoint_owner = DEFAULT_OWNER;

        return $this->constructEndpoint($product->endpoint_name, $endpoint_owner, $product->endpoint_version);
    }

    public function createEndpoint(string $endpoint_name, string $account_name, ?string $version=null): Endpoint
    {
        if (count($endpoint_name) == 0){
            throw new MindeeClientException("Custom endpoint requires a valid 'endpoint_name'.");
        }
        $account_name = $this->cleanAccountName($account_name);
        if (!$version || count($version)<1){
            error_log("No version provided for a custom build, will attempt to poll version 1 by default.");
            $version = "1";
        }
        return $this->constructEndpoint($endpoint_name, $account_name, $version);
    }
    private function cutDocPages(LocalInputSource $input_doc, PageOptions $page_options)
    {

    }

    private function makeParseRequest(
        Inference    $prediction_type,
        InputSource  $input_doc,
        Endpoint     $endpoint,
        bool         $include_words,
        bool         $close_file,
        ?PageOptions $page_options,
        bool         $cropper
    ): PredictReponse
    {
        if ($page_options) {
            if ($input_doc instanceof LocalInputSource){
                $this->cutDocPages($input_doc, $page_options);
            } else {
                throw new MindeeApiException("Cannot edit non-local input sources.");
            }
        }
        $response = $endpoint->predictRequestPost($input_doc, $include_words, $close_file, $cropper);
        if (!$response['ok']) {
            throw handle_error($endpoint->settings->endpointName, $response, $response['status_code']);
        }

        return new PredictReponse($prediction_type, $response);
    }

    public function parse(
        Inference    $prediction_type,
        InputSource  $input_doc,
        ?bool        $include_words = false,
        ?bool        $close_file = true,
        ?PageOptions $page_options = null,
        ?bool        $cropper = false,
        ?Endpoint    $custom_endpoint = null
    ): PredictReponse
    {
        $endpoint = $custom_endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );

        return $this->makeParseRequest($prediction_type, $input_doc, $endpoint, $include_words, $close_file, $page_options, $cropper);
    }
}
