<?php

namespace Mindee;

use Mindee\Error\MindeeClientException;
use Mindee\Error\MindeeHttpException;
use Mindee\Input\EnqueueAndParseMethodOptions;
use Mindee\Input\InputSource;
use Mindee\Input\PathInput;
use Mindee\Input\PredictMethodOptions;
use Mindee\Error\MindeeApiException;
use Mindee\Http\Endpoint;
use Mindee\Http\MindeeApi;
use Mindee\Input\Base64Input;
use Mindee\Input\BytesInput;
use Mindee\Input\FileInput;
use Mindee\Input\LocalInputSource;
use Mindee\Input\PageOptions;
use Mindee\Input\URLInputSource;
use Mindee\Parsing\Common\AsyncPredictResponse;
use Mindee\Parsing\Common\PredictResponse;

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

    private function cleanAccountName(string $account_name): string
    {
        if (!$account_name || strlen(trim($account_name)) < 1) {
            error_log("No account name provided for custom build. " . DEFAULT_OWNER . " will be used by default.");
            return DEFAULT_OWNER;
        }
        return $account_name;
    }

    private function constructOTSEndpoint($product): Endpoint
    {
        try {
            $reflection = new \ReflectionClass($product);
            $endpoint_name = $reflection->getStaticPropertyValue("endpoint_name");
            $endpoint_version = $reflection->getStaticPropertyValue("endpoint_version");
        } catch (\ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $product);
        }
        if ($endpoint_name == 'custom') {
            throw new MindeeApiException('Please create an endpoint manually before sending requests to a custom build.');
        }
        $endpoint_owner = DEFAULT_OWNER;

        return $this->constructEndpoint($endpoint_name, $endpoint_owner, $endpoint_version);
    }

    public function createEndpoint(string $endpoint_name, string $account_name, ?string $version = null): Endpoint
    {
        if (strlen($endpoint_name) == 0) {
            throw new MindeeClientException("Custom endpoint requires a valid 'endpoint_name'.");
        }
        $account_name = $this->cleanAccountName($account_name);
        if (!$version || strlen($version) < 1) {
            error_log("Notice: no version provided for a custom build, will attempt to poll version 1 by default.");
            $version = "1";
        }
        return $this->constructEndpoint($endpoint_name, $account_name, $version);
    }

    private function cutDocPages(LocalInputSource $input_doc, PageOptions $page_options)
    {

    }

    private function makeParseQueuedRequest(
        string   $prediction_type,
        string   $queue_id,
        Endpoint $endpoint
    ): AsyncPredictResponse
    {
        $queued_response = $endpoint->documentQueueReqGet($queue_id);
        $data_response = json_decode($queued_response['data'], true);
        if (!array_key_exists('api_request', $data_response) || count($data_response["api_request"]["error"]) != 0) {
            throw MindeeHttpException::handle_error($endpoint->settings->endpointName, $data_response, $data_response['api_request']['status_code']);
        }
        return new AsyncPredictResponse($prediction_type, $data_response);
    }

    private function makeEnqueueRequest(
        string               $prediction_type,
        InputSource          $input_doc,
        PredictMethodOptions $options
    ): AsyncPredictResponse
    {
        if ($input_doc instanceof LocalInputSource) {
            $this->cutDocPages($input_doc, $options->pageOptions);
        } else {
            throw new MindeeApiException("Cannot edit non-local input sources.");
        }
        $response = $options->endpoint->predictAsyncRequestPost($input_doc, $options->predictOptions->include_words, $options->closeFile, $options->predictOptions->cropper);
        $data_response = json_decode($response['data'], true);
        if (!array_key_exists('api_request', $data_response) || count($data_response["api_request"]["error"]) != 0) {
            throw MindeeHttpException::handle_error($options->endpoint->settings->endpointName, $data_response, $data_response['api_request']['status_code']);
        }

        return new AsyncPredictResponse($prediction_type, $data_response);
    }

    private function makeParseRequest(
        string               $prediction_type,
        InputSource          $input_doc,
        PredictMethodOptions $options
    ): PredictResponse
    {
        if ($input_doc instanceof LocalInputSource) {
            $this->cutDocPages($input_doc, $options->pageOptions);
        } else {
            throw new MindeeApiException("Cannot edit non-local input sources.");
        }
        $response = $options->endpoint->predictRequestPost($input_doc, $options->predictOptions->include_words, $options->closeFile, $options->predictOptions->cropper);
        $data_response = json_decode($response['data'], true);
        if (!array_key_exists('api_request', $data_response) || count($data_response["api_request"]["error"]) != 0) {
            throw MindeeHttpException::handle_error($options->endpoint->settings->endpointName, $data_response, $data_response['api_request']['status_code']);
        }

        return new PredictResponse($prediction_type, $data_response);
    }

    public function parse(
        string                $prediction_type,
        InputSource           $input_doc,
        ?PredictMethodOptions $options = null
    ): PredictResponse
    {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );

        return $this->makeParseRequest($prediction_type, $input_doc, $options);
    }

    public function enqueueAndParse(
        string                        $prediction_type,
        InputSource                   $input_doc,
        ?PredictMethodOptions         $options = null,
        ?EnqueueAndParseMethodOptions $async_options = null): AsyncPredictResponse
    {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        if ($async_options == null) {
            $async_options = new EnqueueAndParseMethodOptions();
        }

        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );
        $enqueue_response = $this->enqueue($prediction_type, $input_doc, $options);
        error_log("Successfully enqueued document with job id: " . $enqueue_response->job->id);

        sleep($async_options->initialDelaySec);
        $retry_counter = 1;
        $poll_results = $this->parseQueued($prediction_type, $enqueue_response->job->id, $options->endpoint);

        while ($retry_counter < $async_options->maxRetries) {
            if ($poll_results->job->status == "completed") {
                break;
            }
            error_log("Polling server for parsing result with job id: " . $enqueue_response->job->id);
            $retry_counter++;
            sleep($async_options->delaySec);
            $poll_results = $this->parseQueued($prediction_type, $enqueue_response->job->id);
        }
        if ($poll_results->job->status != "completed") {
            throw new MindeeApiException("Couldn't retrieve document " . $enqueue_response->job->id . " after $retry_counter tries.");
        }
        return $poll_results;
    }

    public
    function enqueue(
        string                $prediction_type,
        InputSource           $input_doc,
        ?PredictMethodOptions $options = null): AsyncPredictResponse
    {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );
        return $this->makeEnqueueRequest($prediction_type, $input_doc, $options);
    }

    public
    function parseQueued(
        string                $prediction_type,
        string                $queue_id,
        ?Endpoint $endpoint = null): AsyncPredictResponse
    {
        $endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );
        return $this->makeParseQueuedRequest($prediction_type, $queue_id, $endpoint);
    }
}
