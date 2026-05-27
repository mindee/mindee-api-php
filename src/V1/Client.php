<?php

declare(strict_types=1);

/**
 * Mindee Client.
 *
 * Handles most basic operations of the library.
 */

namespace Mindee\V1;

use Exception;
use Mindee\ClientOptions\PollingOptions;
use Mindee\CustomSleepMixin;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeException;
use Mindee\Error\V1\MindeeV1ClientException;
use Mindee\Error\V1\MindeeV1HttpException;
use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\LocalResponse;
use Mindee\Input\PageOptions;
use Mindee\V1\ClientOptions\PredictMethodOptions;
use Mindee\V1\ClientOptions\WorkflowOptions;
use Mindee\V1\Http\Endpoint;
use Mindee\V1\Http\MindeeApi;
use Mindee\V1\Http\MindeeWorkflowApi;
use Mindee\V1\Http\ResponseValidation;
use Mindee\V1\Http\WorkflowEndpoint;
use Mindee\V1\Parsing\Common\AsyncPredictResponse;
use Mindee\V1\Parsing\Common\PredictResponse;
use Mindee\V1\Parsing\Common\WorkflowResponse;
use Mindee\V1\Product\Generated\GeneratedV1;
use ReflectionClass;
use ReflectionException;

/**
 * Main entrypoint for Mindee operations.
 */
class Client
{
    use CustomSleepMixin;

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
     * Builds a custom endpoint.
     *
     * @param string $endpointName URL of the endpoint.
     * @param string $endpointOwner Name of the endpoint's owner.
     * @param string $endpointVersion Version of the endpoint.
     */
    private function constructEndpoint(
        string $endpointName,
        string $endpointOwner,
        string $endpointVersion
    ): Endpoint {
        $endpointVersion = $endpointVersion !== '' ? $endpointVersion : '1';

        $endpointSettings = new MindeeApi($this->apiKey, $endpointName, $endpointOwner, $endpointVersion);

        return new Endpoint($endpointName, $endpointOwner, $endpointVersion, $endpointSettings);
    }

    /**
     * Cleans the account name.
     *
     * @param string $accountName Name of the endpoint's owner. Replaced by self::DEFAULT_OWNER if absent.
     */
    private function cleanAccountName(string $accountName): string
    {
        if (!$accountName || mb_strlen(trim($accountName), "UTF-8") < 1) {
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
     * @throws MindeeApiException Throws if the product isn't recognized.
     */
    private function constructOTSEndpoint(string $product): Endpoint
    {
        try {
            $reflection = new ReflectionClass($product);
            $endpointName = $reflection->getStaticPropertyValue("endpointName");
            $endpointVersion = $reflection->getStaticPropertyValue("endpointVersion");
        } catch (ReflectionException $e) {
            throw new MindeeApiException(
                "Unable to create custom product " . $product,
                ErrorCode::INTERNAL_LIBRARY_ERROR,
                previous: $e
            );
        }
        if ($endpointName === 'custom') {
            throw new MindeeApiException(
                'Please create an endpoint manually before sending requests to a custom build.',
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $endpointOwner = self::DEFAULT_OWNER;

        return $this->constructEndpoint($endpointName, $endpointOwner, $endpointVersion);
    }

    /**
     * Adds a custom endpoint, created using the Mindee API Builder.
     *
     * @param string $endpointName URL of the endpoint.
     * @param string $accountName Name of the endpoint's owner.
     * @param string|null $version Version of the endpoint.
     * @throws MindeeV1ClientException Throws if a custom endpoint name isn't provided.
     */
    public function createEndpoint(string $endpointName, string $accountName, ?string $version = null): Endpoint
    {
        if (mb_strlen($endpointName, "UTF-8") === 0) {
            throw new MindeeV1ClientException(
                "Custom endpoint requires a valid 'endpoint_name'.",
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $accountName = $this->cleanAccountName($accountName);
        if (empty($version)) {
            error_log("Notice: no version provided for a custom build, will attempt to poll version 1 by default.");
            $version = "1";
        }
        return $this->constructEndpoint($endpointName, $accountName, $version);
    }

    /**
     * Cut the pages of a PDF following the detailed operations.
     *
     * @param LocalInputSource $inputDoc Input PDF file.
     * @param PageOptions $pageOptions Options to apply to the PDF file.
     */
    private function cutDocPages(LocalInputSource $inputDoc, PageOptions $pageOptions): void
    {
        $inputDoc->applyPageOptions($pageOptions);
    }

    /**
     * Makes the request to retrieve an async document.
     *
     * @param string $predictionType Name of the product's class.
     * @param string $queueId ID of the queue.
     * @param Endpoint $endpoint Endpoint to poll.
     * @throws MindeeV1HttpException Throws if the API sent an error.
     */
    private function makeParseQueuedRequest(
        string $predictionType,
        string $queueId,
        Endpoint $endpoint
    ): AsyncPredictResponse {
        $queuedResponse = ResponseValidation::cleanRequestData($endpoint->documentQueueReqGet($queueId));
        if (!ResponseValidation::isValidAsyncResponse($queuedResponse)) {
            throw MindeeV1HttpException::handleError(
                $endpoint->settings->endpointName,
                $queuedResponse
            );
        }
        return new AsyncPredictResponse($predictionType, $queuedResponse['data']);
    }

    /**
     * Makes the request to send a document to an asynchronous endpoint.
     *
     * @param string $predictionType Name of the product's class.
     * @param InputSource $inputDoc Input file.
     * @param PredictMethodOptions $options Prediction Options.
     * @throws MindeeV1HttpException Throws if the API sent an error.
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
                throw new MindeeApiException(
                    "Cannot edit non-local input sources.",
                    ErrorCode::USER_OPERATION_ERROR
                );
            }
        }
        $response = ResponseValidation::cleanRequestData(
            $options->endpoint->predictAsyncRequestPost(
                $inputDoc,
                $options
            )
        );
        if (!ResponseValidation::isValidAsyncResponse($response)) {
            throw MindeeV1HttpException::handleError(
                $options->endpoint->settings->endpointName,
                $response
            );
        }
        return new AsyncPredictResponse($predictionType, $response['data']);
    }

    /**
     * Makes the request to send a document to a workflow.
     *
     * @param string $predictionType Name of the product's class.
     * @param InputSource $inputDoc Input file.
     * @param string $workflowId ID of the workflow.
     * @param PredictMethodOptions $options Prediction Options.
     * @throws MindeeV1HttpException Throws if the API sent an error.
     * @throws MindeeApiException Throws if the API sent an error,
     *                            or if the prediction type isn't recognized or if a field can't be deserialized.
     */
    private function makeWorkflowExecutionRequest(
        string $predictionType,
        InputSource $inputDoc,
        string $workflowId,
        PredictMethodOptions $options
    ): WorkflowResponse {
        $workflowRouterSettings = new MindeeWorkflowApi($this->apiKey, $workflowId);
        $options->endpoint = new WorkflowEndpoint($workflowRouterSettings);
        if (!$options->pageOptions->isEmpty()) {
            if ($inputDoc instanceof LocalInputSource) {
                $this->cutDocPages($inputDoc, $options->pageOptions);
            } else {
                throw new MindeeApiException(
                    "Cannot edit non-local input sources.",
                    ErrorCode::USER_OPERATION_ERROR
                );
            }
        }
        $response = ResponseValidation::cleanRequestData($options->endpoint->executeWorkflowRequestPost(
            $inputDoc,
            $options->workflowOptions
        ));
        if (!ResponseValidation::isValidWorkflowResponse($response)) {
            throw MindeeV1HttpException::handleError(
                "workflows/$workflowId/executions",
                $response
            );
        }
        try {
            return new WorkflowResponse($predictionType, $response['data']);
        } catch (Exception $e) {
            throw new MindeeApiException(
                "Unable to create workflow response for $predictionType",
                ErrorCode::API_UNPROCESSABLE_ENTITY,
                previous: $e
            );
        }
    }

    /**
     * Makes the request to send a document to a synchronous endpoint.
     *
     * @param string $predictionType Name of the product's class.
     * @param InputSource $inputDoc Input file.
     * @param PredictMethodOptions $options Prediction Options.
     * @throws MindeeV1HttpException Throws if the API sent an error.
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
                throw new MindeeApiException(
                    "Cannot edit non-local input sources.",
                    ErrorCode::USER_OPERATION_ERROR
                );
            }
        }
        $response = ResponseValidation::cleanRequestData($options->endpoint->predictRequestPost(
            $inputDoc,
            $options,
        ));
        if (!ResponseValidation::isValidSyncResponse($response)) {
            throw MindeeV1HttpException::handleError(
                $options->endpoint->settings->endpointName,
                $response
            );
        }

        return new PredictResponse($predictionType, $response["data"]);
    }

    /**
     * Call prediction API on the document and parse the results.
     *
     * @param string $predictionType Name of the product's class.
     * @param InputSource $inputDoc Input file.
     * @param PredictMethodOptions|null $options Prediction options.
     * @param PageOptions|null $pageOptions Options to apply to the PDF file.
     */
    public function parse(
        string $predictionType,
        InputSource $inputDoc,
        ?PredictMethodOptions $options = null,
        ?PageOptions $pageOptions = null
    ): PredictResponse {
        if (null === $options) {
            $options = new PredictMethodOptions();
        }
        if ($pageOptions !== null && $inputDoc instanceof LocalInputSource && $inputDoc->isPdf()) {
            $this->cutDocPages($inputDoc, $pageOptions);
        }
        $options->endpoint ??= $this->constructOTSEndpoint(
            $predictionType,
        );

        return $this->makeParseRequest($predictionType, $inputDoc, $options);
    }

    /**
     * Enqueues a document and automatically polls the response. Asynchronous calls only.
     *
     * @param string $predictionType Name of the product's class.
     * @param InputSource $inputDoc Input file.
     * @param PredictMethodOptions|null $options Prediction Options.
     * @param PollingOptions|null $asyncOptions Async Options. Manages timers.
     * @param PageOptions|null $pageOptions Options to apply to the PDF file.
     * @throws MindeeApiException Throws if the document couldn't be retrieved in time.
     */
    public function enqueueAndParse(
        string $predictionType,
        InputSource $inputDoc,
        ?PredictMethodOptions $options = null,
        ?PollingOptions $asyncOptions = null,
        ?PageOptions $pageOptions = null
    ): AsyncPredictResponse {
        if (null === $options) {
            $options = new PredictMethodOptions();
        }
        if (null === $asyncOptions) {
            $asyncOptions = new PollingOptions();
        }

        $options->endpoint ??= $this->constructOTSEndpoint(
            $predictionType,
        );

        $enqueueResponse = $this->enqueue(
            $predictionType,
            $inputDoc,
            $options,
            $pageOptions
        );
        error_log("Successfully enqueued document with job id: " . $enqueueResponse->job->id);

        $this->customSleep($asyncOptions->initialDelaySec);
        $retryCounter = 1;
        $pollResults = $this->parseQueued($predictionType, $enqueueResponse->job->id, $options->endpoint);

        while ($retryCounter < $asyncOptions->maxRetries) {
            if ($pollResults->job->status === "completed") {
                break;
            }
            error_log("Polling server for parsing result with job id: " . $enqueueResponse->job->id);
            $retryCounter++;
            $this->customSleep($asyncOptions->delaySec);
            $pollResults = $this->parseQueued($predictionType, $enqueueResponse->job->id, $options->endpoint);
        }
        if ($pollResults->job->status !== "completed") {
            throw new MindeeApiException(
                "Couldn't retrieve document " . $enqueueResponse->job->id . " after $retryCounter tries.",
                ErrorCode::API_TIMEOUT,
            );
        }
        return $pollResults;
    }

    /**
     * Enqueue a document to an asynchronous endpoint.
     *
     * @param string $predictionType Name of the product's class.
     * @param InputSource $inputDoc Input File.
     * @param PredictMethodOptions|null $options Prediction Options.
     * @param PageOptions|null $pageOptions Options to apply to the PDF file.
     */
    public function enqueue(
        string $predictionType,
        InputSource $inputDoc,
        ?PredictMethodOptions $options = null,
        ?PageOptions $pageOptions = null
    ): AsyncPredictResponse {
        if (null === $options) {
            $options = new PredictMethodOptions();
        }
        if ($pageOptions !== null && $inputDoc instanceof LocalInputSource && $inputDoc->isPdf()) {
            $this->cutDocPages($inputDoc, $pageOptions);
        }
        $options->endpoint ??= $this->constructOTSEndpoint(
            $predictionType,
        );
        return $this->makeEnqueueRequest($predictionType, $inputDoc, $options);
    }

    /**
     * Parses a queued document.
     *
     * @param string $predictionType Name of the product's class.
     * @param string $queueId ID of the queue.
     * @param Endpoint|null $endpoint Endpoint to poll.
     */
    public function parseQueued(
        string $predictionType,
        string $queueId,
        ?Endpoint $endpoint = null
    ): AsyncPredictResponse {
        $endpoint ??= $this->constructOTSEndpoint(
            $predictionType,
        );
        return $this->makeParseQueuedRequest($predictionType, $queueId, $endpoint);
    }

    /**
     * @param string $predictionType Name of the product's class.
     * @param LocalResponse $localResponse Local response to load.
     * @return AsyncPredictResponse|PredictResponse A valid prediction response.
     * @throws MindeeException Throws if the loaded response isn't a valid prediction.
     */
    public function loadPrediction(
        string $predictionType,
        LocalResponse $localResponse
    ): AsyncPredictResponse|PredictResponse {
        try {
            $json = $localResponse->toArray();
            if (isset($json['job'])) {
                return new AsyncPredictResponse($predictionType, $json);
            }
            return new PredictResponse($predictionType, $json);
        } catch (Exception $e) {
            throw new MindeeException(
                "Local response is not a valid prediction.",
                ErrorCode::USER_INPUT_ERROR,
                previous: $e
            );
        }
    }

    /**
     * Sends a document to a workflow.
     *
     * @param InputSource $inputDoc Input File.
     * @param string $workflowId ID of the workflow.
     * @param WorkflowOptions|null $options Prediction Options.
     * @param PageOptions|null $pageOptions Options to apply to the PDF file.
     */
    public function executeWorkflow(
        InputSource $inputDoc,
        string $workflowId,
        ?WorkflowOptions $options = null,
        ?PageOptions $pageOptions = null
    ): WorkflowResponse {
        if (null === $options) {
            $options = new WorkflowOptions();
        }
        if ($pageOptions !== null && $inputDoc instanceof LocalInputSource && $inputDoc->isPdf()) {
            $this->cutDocPages($inputDoc, $pageOptions);
        }

        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setWorkflowOptions($options);
        return $this->makeWorkflowExecutionRequest(
            GeneratedV1::class,
            $inputDoc,
            $workflowId,
            $predictMethodOptions
        );
    }
}
