<?php

namespace Mindee;

use Mindee\Error\MindeeException;
use Mindee\Http\MindeeApiV2;
use Mindee\Input\InferenceParameters;
use Mindee\Input\InputSource;
use Mindee\Parsing\V2\InferenceResponse;
use Mindee\Parsing\V2\JobResponse;

/**
 * Mindee Client V2.
 */
class ClientV2
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
        $this->mindeeApi = new MindeeApiV2($apiKey ?: getenv('MINDEE_V2_API_KEY'));
    }

    /**
     * Send the document to an asynchronous endpoint and return its ID in the queue.
     *
     * @param InputSource         $inputSource File to parse.
     * @param InferenceParameters $params      Parameters relating to prediction options.
     * @return JobResponse A JobResponse containing the job (queue) corresponding to a document.
     * @throws MindeeException Throws if the input document is not provided.
     * @category Asynchronous
     */
    public function enqueueInference(
        InputSource $inputSource,
        InferenceParameters $params
    ): JobResponse {
        return $this->mindeeApi->reqPostInferenceEnqueue($inputSource, $params);
    }

    /**
     * Retrieves an inference.
     *
     * @param string $inferenceId ID of the queue to poll.
     * @return InferenceResponse An InferenceResponse containing a Job.
     * @category Asynchronous
     */
    public function getInference(string $inferenceId): InferenceResponse
    {
        return $this->mindeeApi->reqGetInference($inferenceId);
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
     * @param InputSource         $inputDoc Input document to parse.
     * @param InferenceParameters $params   Parameters relating to prediction options.
     * @return InferenceResponse A response containing parsing results.
     * @throws MindeeException Throws if enqueueing fails, job fails, or times out.
     * @category Synchronous
     */
    public function enqueueAndGetInference(
        InputSource $inputDoc,
        InferenceParameters $params
    ): InferenceResponse {
        $pollingOptions = $params->pollingOptions;

        $enqueueResponse = $this->enqueueInference($inputDoc, $params);

        if (empty($enqueueResponse->job->id)) {
            error_log("Failed enqueueing:\n" . json_encode($enqueueResponse));
            throw new MindeeException("Enqueueing of the document failed.");
        }

        $queueId = $enqueueResponse->job->id;
        error_log("Successfully enqueued document with job id: " . $queueId);

        $this->customSleep($pollingOptions->initialDelaySec);
        $retryCounter = 1;
        $pollResults = $this->getJob($queueId);

        while ($retryCounter < $pollingOptions->maxRetries) {
            if ($pollResults->job->status === "Failed") {
                break;
            }
            if ($pollResults->job->status === "Processed") {
                return $this->getInference($pollResults->job->id);
            }

            error_log(
                "Polling server for parsing result with queueId: " . $queueId .
                ". Attempt number " . $retryCounter . " of " . $pollingOptions->maxRetries .
                ". Job status: " . $pollResults->job->status
            );

            $this->customSleep($pollingOptions->delaySec);
            $pollResults = $this->getJob($queueId);
            $retryCounter++;
        }

        if ($pollResults->job->error) {
            throw new MindeeException(
                "Job failed: " . ($pollResults->job->error->detail ?? 'Unknown error')
            );
        }

        throw new MindeeException(
            "Asynchronous parsing request timed out after " .
            ($pollingOptions->delaySec * $retryCounter) . " seconds"
        );
    }
}
