<?php

/**
 * Mindee Client.
 *
 * Handles most basic operations of the library.
 */

namespace Mindee;

use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeClientException;
use Mindee\Error\MindeeException;
use Mindee\Error\MindeeHttpException;
use Mindee\Http\Endpoint;
use Mindee\Http\MindeeApi;
use Mindee\Http\ResponseValidation;
use Mindee\Input\Base64Input;
use Mindee\Input\BytesInput;
use Mindee\Input\EnqueueAndParseMethodOptions;
use Mindee\Input\FileInput;
use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\LocalResponse;
use Mindee\Input\PageOptions;
use Mindee\Input\PathInput;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\URLInputSource;
use Mindee\Parsing\Common\AsyncPredictResponse;
use Mindee\Parsing\Common\PredictResponse;
use ReflectionClass;
use ReflectionException;

/**
 * Main entrypoint for Mindee operations.
 */
class Client
{
    /**
     * Default owner for API products.
     *
     * Do not change unless you know what you are doing.
     */
    public const DEFAULT_OWNER = 'mindee';
    /**
     * @var string API key for a given client.
     */
    private string $apiKey;

    /**
     * Mindee Client.
     *
     * @param string|null $apiKey Optional API key. Will fall back to environment variable if not provided.
     */
    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?: getenv('MINDEE_API_KEY');
    }

    /**
     * Load a document from an absolute path, as a string.
     *
     * @param string  $filePath Path of the file.
     * @param boolean $fixPDF   Whether the PDF should be fixed or not.
     * @return PathInput
     */
    public function sourceFromPath(string $filePath, bool $fixPDF = false): PathInput
    {
        return new PathInput($filePath, $fixPDF);
    }

    /**
     * Load a document from a normal PHP file object.
     *
     * @param mixed   $file   File object as created from the file() function.
     * @param boolean $fixPDF Whether the PDF should be fixed or not.
     * @return FileInput
     */
    public function sourceFromFile($file, bool $fixPDF = false): FileInput
    {
        return new FileInput($file, $fixPDF);
    }

    /**
     * Load a document from raw bytes.
     *
     * @param string  $fileBytes File object in raw bytes.
     * @param string  $fileName  File name, mandatory.
     * @param boolean $fixPDF    Whether the PDF should be fixed or not.
     * @return BytesInput
     */
    public function sourceFromBytes(string $fileBytes, string $fileName, bool $fixPDF = false): BytesInput
    {
        return new BytesInput($fileBytes, $fileName, $fixPDF);
    }

    /**
     * Load a document from a base64 encoded string.
     *
     * @param string  $fileB64  File object in Base64.
     * @param string  $fileName File name, mandatory.
     * @param boolean $fixPDF   Whether the PDF should be fixed or not.
     * @return Base64Input
     */
    public function sourceFromB64String(string $fileB64, string $fileName, bool $fixPDF = false): Base64Input
    {
        return new Base64Input($fileB64, $fileName, $fixPDF);
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
     * @param string $endpointName    URL of the endpoint.
     * @param string $endpointOwner   Name of the endpoint's owner.
     * @param string $endpointVersion Version of the endpoint.
     * @return Endpoint
     */
    private function constructEndpoint(
        string $endpointName,
        string $endpointOwner,
        string $endpointVersion
    ): Endpoint {
        $endpointVersion = $endpointVersion != null && strlen($endpointVersion) > 0 ? $endpointVersion : '1';

        $endpointSettings = new MindeeApi($this->apiKey, $endpointName, $endpointOwner, $endpointVersion);

        return new Endpoint($endpointName, $endpointOwner, $endpointVersion, $endpointSettings);
    }

    /**
     * Cleans the account name.
     *
     * @param string $accountName Name of the endpoint's owner. Replaced by self::DEFAULT_OWNER if absent.
     * @return string
     */
    private function cleanAccountName(string $accountName): string
    {
        if (!$accountName || strlen(trim($accountName)) < 1) {
            error_log(
                "No account name provided for custom build. " . self::DEFAULT_OWNER . " will be used by default."
            );
            return self::DEFAULT_OWNER;
        }
        return $accountName;
    }

    /**
     * Builds an off-the-shelf endpoint.
     *
     * @param string $product Name of the product's class.
     * @return Endpoint
     * @throws MindeeApiException Throws if the product isn't recognized.
     */
    private function constructOTSEndpoint(string $product): Endpoint
    {
        try {
            $reflection = new ReflectionClass($product);
            $endpointName = $reflection->getStaticPropertyValue("endpointName");
            $endpointVersion = $reflection->getStaticPropertyValue("endpointVersion");
        } catch (ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $product);
        }
        if ($endpointName == 'custom') {
            throw new MindeeApiException(
                'Please create an endpoint manually before sending requests to a custom build.'
            );
        }
        $endpointOwner = self::DEFAULT_OWNER;

        return $this->constructEndpoint($endpointName, $endpointOwner, $endpointVersion);
    }

    /**
     * Adds a custom endpoint, created using the Mindee API Builder.
     *
     * @param string      $endpointName URL of the endpoint.
     * @param string      $accountName  Name of the endpoint's owner.
     * @param string|null $version      Version of the endpoint.
     * @return Endpoint
     * @throws MindeeClientException Throws if a custom endpoint name isn't provided.
     */
    public function createEndpoint(string $endpointName, string $accountName, ?string $version = null): Endpoint
    {
        if (strlen($endpointName) == 0) {
            throw new MindeeClientException("Custom endpoint requires a valid 'endpoint_name'.");
        }
        $accountName = $this->cleanAccountName($accountName);
        if (!$version || strlen($version) < 1) {
            error_log("Notice: no version provided for a custom build, will attempt to poll version 1 by default.");
            $version = "1";
        }
        return $this->constructEndpoint($endpointName, $accountName, $version);
    }

    /**
     * Cut the pages of a PDF following the detailed operations.
     *
     * @param LocalInputSource $inputDoc    Input PDF file.
     * @param PageOptions      $pageOptions Options to apply to the PDF file.
     * @return void
     */
    private function cutDocPages(LocalInputSource $inputDoc, PageOptions $pageOptions)
    {
    }

    /**
     * Makes the request to retrieve an async document.
     *
     * @param string   $predictionType Name of the product's class.
     * @param string   $queueId        ID of the queue.
     * @param Endpoint $endpoint       Endpoint to poll.
     * @return AsyncPredictResponse
     * @throws MindeeHttpException Throws if the API sent an error.
     */
    private function makeParseQueuedRequest(
        string $predictionType,
        string $queueId,
        Endpoint $endpoint
    ): AsyncPredictResponse {
        $queuedResponse = ResponseValidation::cleanRequestData($endpoint->documentQueueReqGet($queueId));
        if (!ResponseValidation::isValidAsyncResponse($queuedResponse)) {
            throw MindeeHttpException::handleError(
                $endpoint->settings->endpointName,
                $queuedResponse
            );
        }
        return new AsyncPredictResponse($predictionType, $queuedResponse['data']);
    }

    /**
     * Makes the request to send a document to an asynchronous endpoint.
     *
     * @param string               $predictionType Name of the product's class.
     * @param InputSource          $inputDoc       Input file.
     * @param PredictMethodOptions $options        Prediction Options.
     * @return AsyncPredictResponse
     * @throws MindeeHttpException Throws if the API sent an error.
     * @throws MindeeApiException Throws if one attempts to edit remote resources.
     */
    private function makeEnqueueRequest(
        string $predictionType,
        InputSource $inputDoc,
        PredictMethodOptions $options
    ): AsyncPredictResponse {
        if (!$options->pageOptions->isEmpty()) {
            if ($inputDoc instanceof LocalInputSource) {
                $this->cutDocPages($inputDoc, $options->pageOptions);
            } else {
                throw new MindeeApiException("Cannot edit non-local input sources.");
            }
        }
        $response = ResponseValidation::cleanRequestData($options->endpoint->predictAsyncRequestPost(
            $inputDoc,
            $options->predictOptions->includeWords,
            $options->closeFile,
            $options->predictOptions->cropper
        ));
        if (!ResponseValidation::isValidAsyncResponse($response)) {
            throw MindeeHttpException::handleError(
                $options->endpoint->settings->endpointName,
                $response
            );
        }
        return new AsyncPredictResponse($predictionType, $response['data']);
    }

    /**
     * Makes the request to send a document to a synchronous endpoint.
     *
     * @param string               $predictionType Name of the product's class.
     * @param InputSource          $inputDoc       Input file.
     * @param PredictMethodOptions $options        Prediction Options.
     * @return PredictResponse
     * @throws MindeeHttpException Throws if the API sent an error.
     * @throws MindeeApiException Throws if one attempts to edit remote resources.
     */
    private function makeParseRequest(
        string $predictionType,
        InputSource $inputDoc,
        PredictMethodOptions $options
    ): PredictResponse {
        if (!$options->pageOptions->isEmpty()) {
            if ($inputDoc instanceof LocalInputSource) {
                $this->cutDocPages($inputDoc, $options->pageOptions);
            } else {
                throw new MindeeApiException("Cannot edit non-local input sources.");
            }
        }
        $response = ResponseValidation::cleanRequestData($options->endpoint->predictRequestPost(
            $inputDoc,
            $options->predictOptions->includeWords,
            $options->closeFile,
            $options->predictOptions->cropper
        ));
        if (!ResponseValidation::isValidSyncResponse($response)) {
            throw MindeeHttpException::handleError(
                $options->endpoint->settings->endpointName,
                $response
            );
        }

        return new PredictResponse($predictionType, $response["data"]);
    }

    /**
     * Call prediction API on the document and parse the results.
     *
     * @param string                    $predictionType Name of the product's class.
     * @param InputSource               $inputDoc       Input file.
     * @param PredictMethodOptions|null $options        Prediction Options.
     * @return PredictResponse
     */
    public function parse(
        string $predictionType,
        InputSource $inputDoc,
        ?PredictMethodOptions $options = null
    ): PredictResponse {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $predictionType,
        );

        return $this->makeParseRequest($predictionType, $inputDoc, $options);
    }

    /**
     * Enqueues a document and automatically polls the response. Asynchronous calls only.
     *
     * @param string                            $predictionType Name of the product's class.
     * @param InputSource                       $inputDoc       Input file.
     * @param PredictMethodOptions|null         $options        Prediction Options.
     * @param EnqueueAndParseMethodOptions|null $asyncOptions   Async Options. Manages timers.
     * @return AsyncPredictResponse
     * @throws MindeeApiException Throws if the document couldn't be retrieved in time.
     */
    public function enqueueAndParse(
        string $predictionType,
        InputSource $inputDoc,
        ?PredictMethodOptions $options = null,
        ?EnqueueAndParseMethodOptions $asyncOptions = null
    ): AsyncPredictResponse {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        if ($asyncOptions == null) {
            $asyncOptions = new EnqueueAndParseMethodOptions();
        }

        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $predictionType,
        );
        $enqueueResponse = $this->enqueue($predictionType, $inputDoc, $options);
        error_log("Successfully enqueued document with job id: " . $enqueueResponse->job->id);

        sleep($asyncOptions->initialDelaySec);
        $retryCounter = 1;
        $pollResults = $this->parseQueued($predictionType, $enqueueResponse->job->id, $options->endpoint);

        while ($retryCounter < $asyncOptions->maxRetries) {
            if ($pollResults->job->status == "completed") {
                break;
            }
            error_log("Polling server for parsing result with job id: " . $enqueueResponse->job->id);
            $retryCounter++;
            sleep($asyncOptions->delaySec);
            $pollResults = $this->parseQueued($predictionType, $enqueueResponse->job->id, $options->endpoint);
        }
        if ($pollResults->job->status != "completed") {
            throw new MindeeApiException(
                "Couldn't retrieve document " . $enqueueResponse->job->id . " after $retryCounter tries."
            );
        }
        return $pollResults;
    }

    /**
     * Enqueue a document to an asynchronous endpoint.
     *
     * @param string                    $predictionType Name of the product's class.
     * @param InputSource               $inputDoc       Input File.
     * @param PredictMethodOptions|null $options        Prediction Options.
     * @return AsyncPredictResponse
     * @throws MindeeHttpException Throws if the API sent an error.
     */
    public function enqueue(
        string $predictionType,
        InputSource $inputDoc,
        ?PredictMethodOptions $options = null
    ): AsyncPredictResponse {
        if ($options == null) {
            $options = new PredictMethodOptions();
        }
        $options->endpoint = $options->endpoint ?? $this->constructOTSEndpoint(
            $predictionType,
        );
        return $this->makeEnqueueRequest($predictionType, $inputDoc, $options);
    }

    /**
     * Parses a queued document.
     *
     * @param string        $predictionType Name of the product's class.
     * @param string        $queueId        ID of the queue.
     * @param Endpoint|null $endpoint       Endpoint to poll.
     * @return AsyncPredictResponse
     */
    public function parseQueued(
        string $predictionType,
        string $queueId,
        ?Endpoint $endpoint = null
    ): AsyncPredictResponse {
        $endpoint = $endpoint ?? $this->constructOTSEndpoint(
            $predictionType,
        );
        return $this->makeParseQueuedRequest($predictionType, $queueId, $endpoint);
    }


    /**
     * @param string        $predictionType Name of the product's class.
     * @param LocalResponse $localResponse  Local response to load.
     * @return AsyncPredictResponse|PredictResponse A valid prediction response.
     * @throws MindeeException Throws if the loaded response isn't a valid prediction.
     */
    public function loadPrediction(
        string $predictionType,
        LocalResponse $localResponse
    ) {
        try {
            $json = $localResponse->toArray();
            if (isset($json['job'])) {
                return new AsyncPredictResponse($predictionType, $json);
            }
            return new PredictResponse($predictionType, $json);
        } catch (\Exception $e) {
            throw new MindeeException("Local response is not a valid prediction.");
        }
    }
}
