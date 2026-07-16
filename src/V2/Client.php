<?php

declare(strict_types=1);

namespace Mindee\V2;

use Mindee\ClientOptions\PollingOptions;
use Mindee\CustomSleepMixin;
use Mindee\Error\MindeeException;
use Mindee\Http\CancellationToken;
use Mindee\Input\InputSource;
use Mindee\V2\ClientOptions\BaseParameters;
use Mindee\V2\Http\MindeeApiV2;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Parsing\Search\SearchResponse;

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
     * @param BaseParameters $params Parameters relating to prediction options.
     * @return JobResponse A JobResponse containing the job (queue) corresponding to a document.
     * @throws MindeeException Throws if the input document is not provided.
     * @category Asynchronous
     */
    public function enqueue(
        InputSource    $inputSource,
        BaseParameters $params
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
        return $this->mindeeApi->reqGetResult($responseClass, $resultId);
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
        return $this->mindeeApi->reqGetJob($jobId);
    }

    /**
     * Send a document to an endpoint and poll the server until the result is sent or
     * until the maximum number of tries is reached.
     *
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param InputSource $inputDoc Input document to parse.
     * @param BaseParameters $params Parameters relating to prediction options.
     * @param PollingOptions|null $pollingOptions Options to apply to the polling.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     * @return BaseResponse A response containing parsing results.
     * @throws MindeeException Throws if enqueueing fails, job fails, or times out.
     */
    public function enqueueAndGetResult(
        string             $responseClass,
        InputSource        $inputDoc,
        BaseParameters     $params,
        ?PollingOptions    $pollingOptions = null,
        ?CancellationToken $cancellationToken = null
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
        error_log("Successfully enqueued document with job ID: " . $jobId);

        $this->customSleep($pollingOptions->initialDelaySec, $cancellationToken);
        $retryCounter = 1;
        $pollResults = $this->getJob($jobId);

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
            $pollResults = $this->getJob($jobId);
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
     * Searches for a list of available models for the given API key.
     * @param string|null $modelName Optional model name to filter by.
     * @param string|null $modelType Optional model type to filter by.
     * @return SearchResponse The list of models matching the criteria.
     */
    public function searchModels(?string $modelName = null, ?string $modelType = null): SearchResponse
    {
        return $this->mindeeApi->searchModels($modelName, $modelType);
    }
}
