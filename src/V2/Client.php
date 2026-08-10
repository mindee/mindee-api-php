<?php

declare(strict_types=1);

namespace Mindee\V2;

use Mindee\ClientOptions\PollingOptions;
use Mindee\CustomSleepMixin;
use Mindee\Error\MindeeException;
use Mindee\Http\CancellationToken;
use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\V2\ClientOptions\BaseAnnotationParameters;
use Mindee\V2\ClientOptions\BaseProductParameters;
use Mindee\V2\ClientOptions\BaseSearchParameters;
use Mindee\V2\Http\MindeeApiV2;
use Mindee\V2\Parsing\BaseRagAnnotationResponse;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Parsing\Search\BaseSearchResponse;
use Mindee\V2\Parsing\Search\ModelSearchResponse;
use Mindee\V2\Product\Extraction\RagDocuments\ExtractionRagAnnotationResponse;
use Mindee\V2\Product\Extraction\RagDocuments\Params\RagDocumentUploadParameters;
use Mindee\V2\Search\Models\ModelSearchParameters;

/**
 * Mindee Client V2.
 */
class Client
{
    use CustomSleepMixin;

    /**
     * @var MindeeApiV2 Mindee API V2.
     */
    protected MindeeApiV2 $mindeeApi;

    /**
     * Mindee Client V2.
     *
     * @param string|null $apiKey Optional API key. Will fall back to environment variable if not provided.
     */
    public function __construct(?string $apiKey = null)
    {
        $this->mindeeApi = new MindeeApiV2($apiKey ?: (getenv('MINDEE_V2_API_KEY') ?: null));
    }

    /**
     * Send the document to an asynchronous endpoint and return its ID in the queue.
     * @param InputSource $inputSource File to parse.
     * @param BaseProductParameters $params Parameters relating to prediction options.
     * @return JobResponse A JobResponse containing the job (queue) corresponding to a document.
     * @throws MindeeException Throws if the input document is not provided.
     * @category Asynchronous
     */
    public function enqueue(
        InputSource    $inputSource,
        BaseProductParameters $params
    ): JobResponse {
        return $this->mindeeApi->reqPostEnqueue($inputSource, $params);
    }


    /**
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $resultUrl URL of the result.
     * @return BaseResponse A response containing parsing results.
     */
    public function getResultFromUrl(
        string $responseClass,
        string $resultUrl
    ): BaseResponse {
        return $this->mindeeApi->reqGetResultFromUrl($responseClass, $resultUrl);
    }

    /**
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $resultId ID of the result.
     * @return BaseResponse A response containing parsing results.
     */
    public function getResult(
        string $responseClass,
        string $resultId
    ): BaseResponse {
        return $this->mindeeApi->reqGetResultById($responseClass, $resultId);
    }

    /**
     * Get the status of an inference that was previously enqueued.
     * Can be used for polling.
     *
     * @param string $jobId ID of the queue to poll.
     * @return JobResponse A JobResponse containing a Job, which also contains a Document if the parsing is complete.
     * @category Asynchronous
     */
    public function getJob(string $jobId): JobResponse
    {
        return $this->mindeeApi->reqGetJobById($jobId);
    }

    /**
     * Get the status of a job from its polling URL.
     * Can be used for polling.
     *
     * @param string $pollingUrl URL to poll to retrieve the job.
     * @return JobResponse A JobResponse containing a Job.
     * @category Asynchronous
     */
    public function getJobFromUrl(string $pollingUrl): JobResponse
    {
        return $this->mindeeApi->reqGetJobFromUrl($pollingUrl);
    }

    /**
     * Send a document to an endpoint and poll the server until the result is sent or
     * until the maximum number of tries is reached.
     *
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param InputSource $inputDoc Input document to parse.
     * @param BaseProductParameters $params Parameters relating to prediction options.
     * @param PollingOptions|null $pollingOptions Options to apply to the polling.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     * @return BaseResponse A response containing parsing results.
     * @throws MindeeException Throws if enqueueing fails, job fails, or times out.
     */
    public function enqueueAndGetResult(
        string                $responseClass,
        InputSource           $inputDoc,
        BaseProductParameters $params,
        ?PollingOptions       $pollingOptions = null,
        ?CancellationToken    $cancellationToken = null
    ): BaseResponse {
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }

        $enqueueResponse = $this->enqueue($inputDoc, $params);

        if (empty($enqueueResponse->job->id)) {
            error_log("Failed enqueueing:\n" . json_encode($enqueueResponse));
            throw new MindeeException("Enqueueing of the document failed.");
        }

        $jobId = $enqueueResponse->job->id;
        $pollingUrl = $enqueueResponse->job->pollingUrl;
        error_log("Successfully enqueued document with job ID: " . $jobId);

        $this->customSleep($pollingOptions->initialDelaySec, $cancellationToken);
        $retryCounter = 1;
        $pollResults = $this->getJobFromUrl($pollingUrl);

        while ($retryCounter < $pollingOptions->maxRetries) {
            if ($pollResults->job->status === "Failed") {
                break;
            }
            if ($pollResults->job->status === "Processed") {
                return $this->getResultFromUrl($responseClass, $pollResults->job->resultUrl);
            }

            error_log(
                "Polling server for parsing result with job ID: " . $jobId
                . ". Attempt number " . $retryCounter . " of " . $pollingOptions->maxRetries
                . ". Job status: " . $pollResults->job->status
            );

            $this->customSleep($pollingOptions->delaySec, $cancellationToken);
            $pollResults = $this->getJobFromUrl($pollingUrl);
            $retryCounter++;
        }

        if ($pollResults->job->error) {
            throw new MindeeException(
                "Job failed: " . ($pollResults->job->error->detail ?? 'Unknown error')
            );
        }

        throw new MindeeException(
            "Asynchronous parsing request timed out after "
            . ($pollingOptions->delaySec * $retryCounter) . " seconds"
        );
    }

    /**
     * Not recommended for general use, prefer uploadAndGetRagDocumentPoll().
     * You will need to poll until the document is ready for use.
     * Add a document to the RAG database.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param LocalInputSource $inputSource Local file to upload.
     * @param RagDocumentUploadParameters $params Upload parameters.
     * @return T
     */
    public function uploadRagDocument(
        string $responseClass,
        LocalInputSource $inputSource,
        RagDocumentUploadParameters $params
    ): BaseRagAnnotationResponse {
        error_log("Adding a document to the RAG database");
        return $this->mindeeApi->reqPostRagDocument($responseClass, $inputSource, $params);
    }

    /**
     * Not recommended for general use, prefer getReadyRagDocumentPoll().
     * You will need to poll until the document is ready for use.
     * Get a document's info and annotations from the RAG database.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $documentId Unique identifier of the RAG document.
     * @return T
     */
    public function getRagDocument(string $responseClass, string $documentId): BaseRagAnnotationResponse
    {
        return $this->mindeeApi->reqGetRagAnnotation($responseClass, $documentId);
    }

    /**
     * Update a document's annotations in the RAG database.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param BaseAnnotationParameters $params Annotation parameters including the document ID and fields to update.
     * @return T
     */
    public function updateRagAnnotation(
        string $responseClass,
        BaseAnnotationParameters $params
    ): BaseRagAnnotationResponse {
        return $this->mindeeApi->reqPatchRagAnnotation($responseClass, $params);
    }

    /**
     * Delete a document from the RAG database.
     * For extraction models only.
     *
     * @param string $documentId Unique identifier of the RAG document to delete.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteExtractionRagDocument(string $documentId): bool
    {
        return $this->mindeeApi->reqDeleteExtractionRagDocument($documentId);
    }

    /**
     * Searches for resources matching the given criteria.
     *
     * @template T of BaseSearchResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param BaseSearchParameters $params Search parameters.
     * @return T
     */
    public function search(string $responseClass, BaseSearchParameters $params): BaseSearchResponse
    {
        return $this->mindeeApi->reqGetSearch($responseClass, $params);
    }

    /**
     * Searches for a list of available models for the given API key.
     * @param string|null $modelName Optional model name to filter by.
     * @param string|null $modelType Optional model type to filter by.
     * @return ModelSearchResponse The list of models matching the criteria.
     * @deprecated Use search(ModelSearchResponse::class, new ModelSearchParameters(...)) instead.
     */
    public function searchModels(?string $modelName = null, ?string $modelType = null): ModelSearchResponse
    {
        return $this->mindeeApi->reqGetSearch(
            ModelSearchResponse::class,
            new ModelSearchParameters($modelName, $modelType)
        );
    }

    /**
     * Add a document to the RAG database and return the initial annotation.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param LocalInputSource $inputSource Local file to upload.
     * @param RagDocumentUploadParameters $params Upload parameters.
     * @param PollingOptions|null $pollingOptions Options to apply to the polling.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     * @return T
     * @throws MindeeException Throws if upload fails or polling times out.
     */
    public function uploadAndGetRagDocumentPoll(
        string $responseClass,
        LocalInputSource $inputSource,
        RagDocumentUploadParameters $params,
        ?PollingOptions $pollingOptions = null,
        ?CancellationToken $cancellationToken = null
    ): BaseRagAnnotationResponse {
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }
        $initialResponse = $this->uploadRagDocument($responseClass, $inputSource, $params);
        return $this->pollForRagDocument($responseClass, $initialResponse, $pollingOptions, $cancellationToken);
    }

    /**
     * Get a document's info and annotations from the RAG database.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $documentId Unique identifier of the RAG document.
     * @param PollingOptions|null $pollingOptions Options to apply to the polling.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     * @return T
     * @throws MindeeException Throws if polling times out.
     */
    public function getReadyRagDocumentPoll(
        string $responseClass,
        string $documentId,
        ?PollingOptions $pollingOptions = null,
        ?CancellationToken $cancellationToken = null
    ): BaseRagAnnotationResponse {
        $initialResponse = $this->getRagDocument($responseClass, $documentId);
        if ($initialResponse->status !== "Processing") {
            return $initialResponse;
        }
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }
        return $this->pollForRagDocument($responseClass, $initialResponse, $pollingOptions, $cancellationToken);
    }

    /**
     * Update a document's annotations in the RAG database.
     *
     * @template T of ExtractionRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param BaseAnnotationParameters $params Annotation parameters including the document ID and fields to update.
     * @param PollingOptions|null $pollingOptions Options to apply to the polling.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     * @throws MindeeException Throws if polling times out.
     */
    public function updateAndGetRagAnnotationPoll(
        string $responseClass,
        BaseAnnotationParameters $params,
        ?PollingOptions $pollingOptions = null,
        ?CancellationToken $cancellationToken = null
    ): BaseRagAnnotationResponse {
        error_log("Updating RAG document ID: " . $params->documentId);
        $initialResponse = $this->updateRagAnnotation($responseClass, $params);
        if ($initialResponse->status !== "Processing") {
            return $initialResponse;
        }
        if (!$pollingOptions) {
            $pollingOptions = new PollingOptions();
        }
        return $this->pollForRagDocument($responseClass, $initialResponse, $pollingOptions, $cancellationToken);
    }

    /**
     * Poll until the RAG document is finished processing or the max number of attempts is reached.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param BaseRagAnnotationResponse $initialResponse Initial annotation response.
     * @param PollingOptions $pollingOptions Options to apply to the polling.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     * @return T
     * @throws MindeeException Throws if the job fails or polling times out.
     */
    private function pollForRagDocument(
        string $responseClass,
        BaseRagAnnotationResponse $initialResponse,
        PollingOptions $pollingOptions,
        ?CancellationToken $cancellationToken = null
    ): BaseRagAnnotationResponse {
        $documentId = $initialResponse->id;
        $maxRetries = $pollingOptions->maxRetries + 1;
        $this->customSleep($pollingOptions->initialDelaySec, $cancellationToken);

        $retryCounter = 1;
        while ($retryCounter < $maxRetries) {
            $this->customSleep($pollingOptions->delaySec, $cancellationToken);
            $response = $this->getRagDocument($responseClass, $documentId);
            $retryCounter++;
            switch ($response->status) {
                case "Processing":
                    break;
                case "Failed":
                    throw new MindeeException("Job failed without an error payload.");
                default:
                    return $response;
            }
        }
        throw new MindeeException("RAG polling not complete after $retryCounter attempts.");
    }
}
