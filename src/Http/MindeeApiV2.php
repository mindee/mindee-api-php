<?php

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\Http;

use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeException;

// phpcs:disable
include_once(dirname(__DIR__) . '/version.php');

// phpcs:enable

use Mindee\Error\MindeeV2HttpException;
use Mindee\Error\MindeeV2HttpUnknownException;
use Mindee\Input\InferenceParameters;
use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\URLInputSource;
use Mindee\Parsing\V2\ErrorResponse;
use Mindee\Parsing\V2\InferenceResponse;
use Mindee\Parsing\V2\JobResponse;

use const Mindee\VERSION;

/**
 * Default key name for the API key entry in environment variables.
 */
const API_V2_KEY_ENV_NAME = 'MINDEE_V2_API_KEY';

/**
 * Default key name for the Base URL in environment variables.
 */
const API_V2_BASE_URL_ENV_NAME = 'MINDEE_V2_BASE_URL';

/**
 * Default URL prefix for API calls.
 */
const API_V2_BASE_URL_DEFAULT = 'https://api-v2.mindee.net/v2';

/**
 * Default key name for CURL request timeout in environment variables.
 */
const API_V2_REQUEST_TIMEOUT_ENV_NAME = 'MINDEE_V2_REQUEST_TIMEOUT';
/**
 * Default timeout value for curl requests.
 */
const API_V2_TIMEOUT_DEFAULT = 120;

/**
 * Data class containing settings for endpoints.
 */
class MindeeApiV2
{
    /**
     * Get the User Agent to send for API calls.
     * @return string
     */
    private function getUserAgent(): string
    {
        switch (PHP_OS_FAMILY) {
            case "Darwin":
                $os = "macos";
                break;
            default:
                $os = strtolower(PHP_OS_FAMILY);
        }
        return 'mindee-api-php@v' . VERSION . ' php-v' . PHP_VERSION . ' ' . $os;
    }

    /**
     * @var string|null API key.
     */
    public ?string $apiKey;
    /**
     * @var integer Timeout for the request, in ms.
     */
    public int $requestTimeout;
    /**
     * @var string Base for the root url. Used for testing purposes.
     */
    public string $baseUrl;

    /**
     * @param string|null $apiKey API key.
     * @return void
     * @throws MindeeException Throws if the API key specified is invalid.
     */
    public function __construct(?string $apiKey)
    {
        $this->setApiKey($apiKey);
        $this->baseUrl = API_V2_BASE_URL_DEFAULT;
        $this->requestTimeout = API_V2_TIMEOUT_DEFAULT;
        $this->setFromEnv();
        if (!$this->apiKey || strlen($this->apiKey) == 0) {
            throw new MindeeException(
                "Missing API key for call," .
                " check your Client configuration.You can set this using the " .
                API_KEY_ENV_NAME . ' environment variable.',
                ErrorCode::USER_INPUT_ERROR
            );
        }
    }

    /**
     * Sets the base url.
     *
     * @param string $value Value for the base Url.
     * @return void
     */
    protected function setBaseUrl(string $value): void
    {
        $this->baseUrl = $value;
    }

    /**
     * Sets values from environment, if needed.
     *
     * @return void
     */
    private function setFromEnv(): void
    {
        $envVars = [
            API_V2_BASE_URL_ENV_NAME => [$this, 'setBaseUrl'],
            API_V2_REQUEST_TIMEOUT_ENV_NAME => [$this, 'setTimeout'],
        ];
        foreach ($envVars as $key => $func) {
            $envVal = getenv($key) ? getenv($key) : '';
            if ($envVal) {
                call_user_func($func, $envVal);
                error_log('Value ' . $key . ' was set from env.');
            }
        }
    }


    /**
     * Sets the API key.
     *
     * @param string|null $apiKey Optional API key.
     * @return void
     */
    protected function setApiKey(?string $apiKey = null): void
    {
        $envVal = !getenv(API_V2_KEY_ENV_NAME) ? '' : getenv(API_V2_KEY_ENV_NAME);
        if (!$apiKey) {
            error_log('API key set from environment');
            $this->apiKey = $envVal;
        } else {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * @param InputSource         $inputDoc Input document.
     * @param InferenceParameters $params   Parameters for the inference.
     * @return JobResponse                  Server response wrapped in a JobResponse object.
     * @throws MindeeException Throws if the model ID is not provided.
     */
    public function reqPostInferenceEnqueue(InputSource $inputDoc, InferenceParameters $params): JobResponse
    {
        if (!isset($params->modelId)) {
            throw new MindeeException("Model ID must be provided.", ErrorCode::USER_INPUT_ERROR);
        }
        $response = $this->documentEnqueuePost($inputDoc, $params);
        return $this->processResponse($response, JobResponse::class);
    }


    /**
     * Process the HTTP response and return the appropriate response object.
     *
     * @param array  $result       Raw HTTP response array with 'data' and 'code' keys.
     * @param string $responseType Class name of the response type to instantiate.
     * @return JobResponse|InferenceResponse The processed response object.
     * @throws MindeeException Throws if HTTP status indicates an error or deserialization fails.
     * @throws MindeeV2HttpException Throws if the HTTP status indicates an error.
     * @throws MindeeV2HttpUnknownException Throws if the server sends an unexpected reply.
     */
    private function processResponse(array $result, string $responseType): InferenceResponse|JobResponse
    {
        $statusCode = $result['code'] ?? -1;

        if ($statusCode > 399 || $statusCode < 200) {
            $responseData = json_decode($result['data'], true);

            if ($responseData && isset($responseData['status'])) {
                throw new MindeeV2HttpException(new ErrorResponse($responseData));
            }
            throw new MindeeV2HttpUnknownException(json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }

        try {
            $responseData = json_decode($result['data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new MindeeException('JSON decode error: ' . json_last_error_msg());
            }

            return new $responseType($responseData);
        } catch (Exception $e) {
            error_log("Raised '{$e->getMessage()}' Couldn't deserialize response object:\n" . $result['data']);
            throw new MindeeException("Couldn't deserialize response object.", ErrorCode::API_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Requests the job of a queued document from the API.
     * Throws an error if the server's response contains one.
     * @param string $inferenceId ID of the inference.
     * @return InferenceResponse
     * @throws MindeeException Throws if the server's response contains an error.
     * @throws MindeeException Throws if the inference ID is not provided.
     */
    public function reqGetInference(string $inferenceId): InferenceResponse
    {
        if (!isset($inferenceId)) {
            throw new MindeeException("Inference ID must be provided.", ErrorCode::USER_INPUT_ERROR);
        }
        $response = $this->inferenceGetRequest($inferenceId);
        return $this->processResponse($response, InferenceResponse::class);
    }

    /**
     * Requests the job of a queued document from the API.
     * Throws an error if the server's response contains one.
     * @param string $jobId ID of the inference.
     * @return JobResponse Server response wrapped in a JobResponse object.
     * @throws MindeeException Throws if the server's response contains an error.
     * @throws MindeeException Throws if the inference ID is not provided.
     */
    public function reqGetJob(string $jobId): JobResponse
    {
        if (!isset($jobId)) {
            throw new MindeeException("Inference ID must be provided.", ErrorCode::USER_INPUT_ERROR);
        }
        $response = $this->jobGetRequest($jobId);
        return $this->processResponse($response, JobResponse::class);
    }

    /**
     * Init a CURL channel with common params.
     * @return false|resource Returns a valid CURL channel.
     */
    private function initChannel()
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: ' . $this->apiKey,
            ]
        );
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->requestTimeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        return $ch;
    }

    /**
     * Makes a GET call to retrieve a job.
     * @param string $jobId ID of the job.
     * @return array Server response.
     */
    private function jobGetRequest(string $jobId): array
    {
        $ch = $this->initChannel();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/jobs/$jobId");
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * Makes a GET call to retrieve an inference.
     * @param string $inferenceId ID of the inference.
     * @return array Server response.
     */
    private function inferenceGetRequest(string $inferenceId): array
    {
        $ch = $this->initChannel();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/inferences/$inferenceId");

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * Starts a CURL session using POST.
     *
     * @param InputSource         $inputSource File to upload.
     * @param InferenceParameters $params      Inference parameters.
     * @return array
     * @throws MindeeException Throws if the cURL operation doesn't go succeed.
     */
    private function documentEnqueuePost(
        InputSource $inputSource,
        InferenceParameters $params
    ): array {
        $ch = $this->initChannel();
        $postFields = ['model_id' => $params->modelId];
        if ($inputSource instanceof URLInputSource) {
            $postFields['url'] = $inputSource->url;
        } elseif ($inputSource instanceof LocalInputSource) {
            $inputSource->checkNeedsFix();
            $postFields['file'] = $inputSource->fileObject;
        }

        if (isset($params->rawText)) {
            $postFields['raw_text'] = $params->rawText ? 'true' : 'false';
        }
        if (isset($params->polygon)) {
            $postFields['polygon'] = $params->polygon ? 'true' : 'false';
        }
        if (isset($params->confidence)) {
            $postFields['confidence'] = $params->confidence ? 'true' : 'false';
        }
        if (isset($params->rag)) {
            $postFields['rag'] = $params->rag ? 'true' : 'false';
        }
        if (isset($params->webhooksIds) && count($params->webhooksIds) > 0) {
            if (PHP_VERSION_ID < 80200 && count($params->webhooksIds) > 1) {
                # NOTE: see https://bugs.php.net/bug.php?id=51634
                error_log("PHP version is too low to support webbook array destructuring.
                \nOnly the first webhook ID will be sent to the server.");
                $postFields['webhook_ids'] = $params->webhooksIds[0];
            } else {
                $postFields['webhook_ids'] = $params->webhooksIds;
            }
        }
        if (isset($params->alias)) {
            $postFields['alias'] = $params->alias;
        }
        if (isset($params->textContext)) {
            $postFields['text_context'] = $params->textContext;
        }
        if (isset($params->dataSchema)) {
            $postFields['data_schema'] = strval($params->dataSchema);
        }

        $url = $this->baseUrl . '/inferences/enqueue';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        $curlError = curl_error($ch);
        if (!empty($curlError)) {
            throw new MindeeException("cURL error:\n$curlError");
        }

        curl_close($ch);

        return $resp;
    }
}
