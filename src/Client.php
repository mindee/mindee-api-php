<?php

/**
 * Mindee Client.
 *
 * Handles most basic operations of the library.
 */

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

/**
 * Default owner for API products.
 *
 * Do not change unless you know what you are doing.
 */
const DEFAULT_OWNER = 'mindee';
/**
 * Main entrypoint for Mindee operations.
 */
class Client
{
    /**
     * @var string API key for a given client.
     */
    private string $apiKey;

    /**
     * Mindee Client.
     */
    public function __construct()
    {
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    /**
     * Load a document from an absolute path, as a string.
     *
     * @param string $file_path Path of the file.
     * @return PathInput
     */
    public function sourceFromPath(string $file_path): PathInput
    {
        return new PathInput($file_path);
    }

    /**
     * Load a document from a normal PHP file object.
     *
     * @param array $file File object as created from the file() function.
     * @return FileInput
     */
    public function sourceFromFile(array $file): FileInput
    {
        return new FileInput($file);
    }

    /**
     * Load a document from raw bytes.
     *
     * @param string $file_bytes File object in raw bytes.
     * @param string $file_name  File name, mandatory.
     * @return BytesInput
     */
    public function sourceFromBytes(string $file_bytes, string $file_name): BytesInput
    {
        return new BytesInput($file_bytes, $file_name);
    }

    /**
     * Load a document from a base64 encoded string.
     *
     * @param string $file_b64  File object in Base64.
     * @param string $file_name File name, mandatory.
     * @return Base64Input
     */
    public function sourceFromb64String(string $file_b64, string $file_name): Base64Input
    {
        return new Base64Input($file_b64, $file_name);
    }

    /**
     * Load a document from an URL.
     *
     * @param string $url File URL. Must start with "https://".
     * @return URLInputSource
     */
    public function sourceFromUrl(string $url): URLInputSource
    {
        return new URLInputSource($url);
    }

    /**
     * Builds a custom endpoint.
     *
     * @param string $endpoint_name    URL of the endpoint.
     * @param string $endpoint_owner   Name of the endpoint's owner.
     * @param string $endpoint_version Version of the endpoint.
     * @return Endpoint
     */
    private function constructEndpoint(
        string $endpoint_name,
        string $endpoint_owner,
        string $endpoint_version
    ): Endpoint {
        $endpoint_version = $endpoint_version != null && strlen($endpoint_version) > 0 ? $endpoint_version : '1';

        $endpoint_settings = new MindeeApi($this->apiKey, $endpoint_name, $endpoint_owner, $endpoint_version);

        return new Endpoint($endpoint_name, $endpoint_owner, $endpoint_version, $endpoint_settings);
    }

    /**
     * Cleans the account name.
     *
     * @param string $account_name Name of the endpoint's owner. Replaced by DEFAULT_OWNER if absent.
     * @return string
     */
    private function cleanAccountName(string $account_name): string
    {
        if (!$account_name || strlen(trim($account_name)) < 1) {
            error_log("No account name provided for custom build. " . DEFAULT_OWNER . " will be used by default.");
            return DEFAULT_OWNER;
        }
        return $account_name;
    }

    /**
     * Builds an off-the-shelf endpoint.
     *
     * @param string $product Name of the product's class.
     * @return Endpoint
     * @throws \Mindee\Error\MindeeApiException Throws if the product isn't recognized.
     */
    private function constructOTSEndpoint(string $product): Endpoint
    {
        try {
            $reflection = new \ReflectionClass($product);
            $endpoint_name = $reflection->getStaticPropertyValue("endpoint_name");
            $endpoint_version = $reflection->getStaticPropertyValue("endpoint_version");
        } catch (\ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $product);
        }
        if ($endpoint_name == 'custom') {
            throw new MindeeApiException(
                'Please create an endpoint manually before sending requests to a custom build.'
            );
        }
        $endpoint_owner = DEFAULT_OWNER;

        return $this->constructEndpoint($endpoint_name, $endpoint_owner, $endpoint_version);
    }

    /**
     * Adds a custom endpoint, created using the Mindee API Builder.
     *
     * @param string      $endpoint_name URL of the endpoint.
     * @param string      $account_name  Name of the endpoint's owner.
     * @param string|null $version       Version of the endpoint.
     * @return Endpoint
     * @throws \Mindee\Error\MindeeClientException Throws if a custom endpoint name isn't provided.
     */
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

    /**
     * Cut the pages of a PDF following the detailed operations.
     *
     * @param LocalInputSource $input_doc    Input PDF file.
     * @param PageOptions      $page_options Options to apply to the PDF file.
     * @return void
     */
    private function cutDocPages(LocalInputSource $input_doc, PageOptions $page_options)
    {
    }

    /**
     * Makes the request to retrieve an async document.
     *
     * @param string   $prediction_type Name of the product's class.
     * @param string   $queue_id        ID of the queue.
     * @param Endpoint $endpoint        Endpoint to poll.
     * @return AsyncPredictResponse
     * @throws \Mindee\Error\MindeeHttpException Throws if the API sent an error.
     */
    private function makeParseQueuedRequest(
        string $prediction_type,
        string $queue_id,
        Endpoint $endpoint
    ): AsyncPredictResponse {
        $queued_response = $endpoint->documentQueueReqGet($queue_id);
        $data_response = json_decode($queued_response['data'], true);
        if (!array_key_exists('api_request', $data_response) || count($data_response["api_request"]["error"]) != 0) {
            throw MindeeHttpException::handleError(
                $endpoint->settings->endpointName,
                $data_response,
                $data_response['api_request']['status_code']
            );
        }
        return new AsyncPredictResponse($prediction_type, $data_response);
    }

    /**
     * Makes the request to send a document to an asynchronous endpoint.
     *
     * @param string               $prediction_type Name of the product's class.
     * @param InputSource          $input_doc       Input file.
     * @param PredictMethodOptions $options         Prediction Options.
     * @return AsyncPredictResponse
     * @throws \Mindee\Error\MindeeHttpException Throws if the API sent an error.
     * @throws \Mindee\Error\MindeeApiException Throws if one attempts to edit remote resources.
     */
    private function makeEnqueueRequest(
        string $prediction_type,
        InputSource $input_doc,
        PredictMethodOptions $options
    ): AsyncPredictResponse {
        if ($input_doc instanceof LocalInputSource) {
            $this->cutDocPages($input_doc, $options->pageOptions);
        } else {
            throw new MindeeApiException("Cannot edit non-local input sources.");
        }
        $response = $options->endpoint->predictAsyncRequestPost(
            $input_doc,
            $options->predictOptions->include_words,
            $options->closeFile,
            $options->predictOptions->cropper
        );
        $data_response = json_decode($response['data'], true);
        if (!array_key_exists('api_request', $data_response) || count($data_response["api_request"]["error"]) != 0) {
            throw MindeeHttpException::handleError(
                $options->endpoint->settings->endpointName,
                $data_response,
                $data_response['api_request']['status_code']
            );
        }

        return new AsyncPredictResponse($prediction_type, $data_response);
    }

    /**
     * Makes the request to send a document to a synchronous endpoint.
     *
     * @param string               $prediction_type Name of the product's class.
     * @param InputSource          $input_doc       Input file.
     * @param PredictMethodOptions $options         Prediction Options.
     * @return PredictResponse
     * @throws \Mindee\Error\MindeeHttpException Throws if the API sent an error.
     * @throws \Mindee\Error\MindeeApiException Throws if one attempts to edit remote resources.
     */
    private function makeParseRequest(
        string $prediction_type,
        InputSource $input_doc,
        PredictMethodOptions $options
    ): PredictResponse {
        if ($input_doc instanceof LocalInputSource) {
            $this->cutDocPages($input_doc, $options->pageOptions);
        } else {
            throw new MindeeApiException("Cannot edit non-local input sources.");
        }
        $response = $options->endpoint->predictRequestPost(
            $input_doc,
            $options->predictOptions->include_words,
            $options->closeFile,
            $options->predictOptions->cropper
        );
        $data_response = json_decode($response['data'], true);
        if (!array_key_exists('api_request', $data_response) || count($data_response["api_request"]["error"]) != 0) {
            throw MindeeHttpException::handleError(
                $options->endpoint->settings->endpointName,
                $data_response,
                $data_response['api_request']['status_code']
            );
        }

        return new PredictResponse($prediction_type, $data_response);
    }

    /**
     * Call prediction API on the document and parse the results.
     *
     * @param string                    $prediction_type Name of the product's class.
     * @param InputSource               $input_doc       Input file.
     * @param PredictMethodOptions|null $options         Prediction Options.
     * @return PredictResponse
     */
    public function parse(
        string $prediction_type,
        InputSource $input_doc,
        ?PredictMethodOptions $options = null
    ): PredictResponse {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );

        return $this->makeParseRequest($prediction_type, $input_doc, $options);
    }

    /**
     * Enqueues a document and automatically polls the response. Asynchronous calls only.
     *
     * @param string                            $prediction_type Name of the product's class.
     * @param InputSource                       $input_doc       Input file.
     * @param PredictMethodOptions|null         $options         Prediction Options.
     * @param EnqueueAndParseMethodOptions|null $async_options   Async Options. Manages timers.
     * @return AsyncPredictResponse
     * @throws \Mindee\Error\MindeeApiException Throws if the document couldn't be retrieved in time.
     */
    public function enqueueAndParse(
        string $prediction_type,
        InputSource $input_doc,
        ?PredictMethodOptions $options = null,
        ?EnqueueAndParseMethodOptions $async_options = null
    ): AsyncPredictResponse {
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
            throw new MindeeApiException(
                "Couldn't retrieve document " . $enqueue_response->job->id . " after $retry_counter tries."
            );
        }
        return $poll_results;
    }

    /**
     * Enqueue a document to an asynchronous endpoint.
     *
     * @param string                    $prediction_type Name of the product's class.
     * @param InputSource               $input_doc       Input File.
     * @param PredictMethodOptions|null $options         Prediction Options.
     * @return AsyncPredictResponse
     * @throws \Mindee\Error\MindeeHttpException Throws if the API sent an error.
     */
    public function enqueue(
        string $prediction_type,
        InputSource $input_doc,
        ?PredictMethodOptions $options = null
    ): AsyncPredictResponse {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );
        return $this->makeEnqueueRequest($prediction_type, $input_doc, $options);
    }

    /**
     * Parses a queued document.
     *
     * @param string        $prediction_type Name of the product's class.
     * @param string        $queue_id        ID of the queue.
     * @param Endpoint|null $endpoint        Endpoint to poll.
     * @return AsyncPredictResponse
     */
    public function parseQueued(
        string $prediction_type,
        string $queue_id,
        ?Endpoint $endpoint = null
    ): AsyncPredictResponse {
        $endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $prediction_type,
        );
        return $this->makeParseQueuedRequest($prediction_type, $queue_id, $endpoint);
    }
}
