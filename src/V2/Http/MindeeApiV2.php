<?php

declare(strict_types=1);

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\V2\Http;

use CurlHandle;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeException;
use Mindee\Http\CurlSslConfig;
use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\UrlInputSource;
use Mindee\V2\ClientOptions\BaseParameters;
use Mindee\V2\ClientOptions\ModelSearchParameters;
use Mindee\V2\ClientOptions\RagDocumentSearchParameters;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Error\MindeeV2HttpUnknownException;
use Mindee\V2\Parsing\Error\ErrorResponse;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Parsing\Search\ModelSearchResponse;
use Mindee\V2\Parsing\Search\RagDocumentSearchResponse;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

use function call_user_func;
use function dirname;

use const Mindee\VERSION;

// phpcs:disable
include_once(dirname(__DIR__, 2) . '/version.php');

// phpcs:enable

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
const API_V2_BASE_URL_DEFAULT = 'https://api-v2.mindee.net';

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
     */
    private function getUserAgent(): string
    {
        $os = match (PHP_OS_FAMILY) {
            "Darwin" => "macos",
            default => strtolower(PHP_OS_FAMILY),
        };
        return 'mindee-api-php@v' . VERSION . ' php-v' . PHP_VERSION . ' ' . $os;
    }

    /**
     * @var string|null API key.
     */
    public ?string $apiKey = null;
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
        $this->setAPIKey($apiKey);
        $this->baseUrl = API_V2_BASE_URL_DEFAULT;
        $this->requestTimeout = API_V2_TIMEOUT_DEFAULT;
        $this->setFromEnv();
        if ($this->apiKey === '') {
            throw new MindeeException(
                "Missing API key for call,"
                . " check your Client configuration.You can set this using the "
                . API_V2_KEY_ENV_NAME . ' environment variable.',
                ErrorCode::USER_INPUT_ERROR
            );
        }
    }

    /**
     * Sets the base url.
     *
     * @param string $value Value for the base Url.
     */
    protected function setBaseUrl(string $value): void
    {
        $this->baseUrl = $value;
    }

    /**
     * Sets values from environment, if needed.
     *
     */
    private function setFromEnv(): void
    {
        $envVars = [
            API_V2_BASE_URL_ENV_NAME => $this->setBaseUrl(...),
            API_V2_REQUEST_TIMEOUT_ENV_NAME => [$this, 'setTimeout'],
        ];
        foreach ($envVars as $key => $func) {
            $envVal = getenv($key) ?: '';
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
     */
    protected function setAPIKey(?string $apiKey = null): void
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
     * @param InputSource $inputDoc Input document.
     * @param BaseParameters $params Parameters for the inference.
     * @return JobResponse Server response wrapped in a JobResponse object.
     * @throws MindeeException Throws if the model ID is not provided.
     */
    public function reqPostEnqueue(InputSource $inputDoc, BaseParameters $params): JobResponse
    {
        if (!isset($params->modelId)) {
            throw new MindeeException("Model ID must be provided.", ErrorCode::USER_INPUT_ERROR);
        }
        $response = $this->documentEnqueuePost($inputDoc, $params);
        return $this->processJobResponse($response);
    }


    /**
     * Process the HTTP response and return the appropriate response object.
     *
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param array<string, integer|float|string|bool|null|array<mixed>> $result Raw HTTP response array with 'data' and 'code' keys.
     * @return T A response containing parsing results.
     * @throws MindeeException Throws if HTTP status indicates an error or deserialization fails.
     */
    private function processResponse(
        string $responseClass,
        array $result
    ): BaseResponse {
        $this->checkValidResponse($result);

        try {
            $responseData = json_decode($result['data'], true);
            $reflectionClass = new ReflectionClass($responseClass);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new MindeeException('JSON decode error: ' . json_last_error_msg());
            }

            /** @var T $instance */
            $instance = $reflectionClass->newInstance($responseData);
            return $instance;
        } catch (Exception $e) {
            error_log("Raised '{$e->getMessage()}' Couldn't deserialize response object:\n" . $result['data']);
            throw new MindeeException("Couldn't deserialize response object.", ErrorCode::API_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Process the HTTP response and return the appropriate response object.
     *
     * @param array<string, integer|float|string|bool|null|array<mixed>> $result Raw HTTP response array with 'data' and 'code' keys.
     * @return JobResponse The processed response object.
     * @throws MindeeException Throws if HTTP status indicates an error or deserialization fails.
     * @throws MindeeApiException Throws if the response type is not recognized.
     */
    private function processJobResponse(array $result): JobResponse
    {
        $this->checkValidResponse($result);

        try {
            $responseData = json_decode($result['data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new MindeeException('JSON decode error: ' . json_last_error_msg());
            }

            return new JobResponse($responseData);
        } catch (Exception $e) {
            error_log("Raised '{$e->getMessage()}' Couldn't deserialize job response:\n" . $result['data']);
            throw new MindeeApiException("Couldn't deserialize response object.", ErrorCode::API_UNPROCESSABLE_ENTITY);
        }
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
        $response = $this->sendGetRequest($this->baseUrl . "/v2/jobs/$jobId");
        return $this->processJobResponse($response);
    }


    /**
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $resultId URL of the result.
     * @return T A response containing parsing results.
     * @throws MindeeException Throws if the server's response contains an error.
     * @throws MindeeApiException Throws if the response class is not valid.
     */
    public function reqGetResult(
        string $responseClass,
        string $resultId
    ): BaseResponse {
        try {
            $slugProperty = new ReflectionProperty($responseClass, 'slug');
        } catch (ReflectionException $e) {
            throw new MindeeApiException(
                "Unable to access slug property of " . $responseClass,
                ErrorCode::INTERNAL_LIBRARY_ERROR,
                $e
            );
        }
        $url = $this->baseUrl . "/v2/products/{$slugProperty->getValue()}/results/$resultId";
        $response = $this->sendGetRequest($url);
        return $this->processResponse($responseClass, $response);
    }

    /**
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $resultUrl URL of the result.
     * @return T A response containing parsing results.
     * @throws MindeeException Throws if the server's response contains an error.
     */
    public function reqGetResultFromUrl(
        string $responseClass,
        string $resultUrl
    ): BaseResponse {
        $response = $this->sendGetRequest($resultUrl);
        return $this->processResponse($responseClass, $response);
    }

    /**
     * Init a CURL channel with common params.
     * @return boolean|CurlHandle Returns a valid CURL channel.
     */
    private function initChannel(): bool|CurlHandle
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
        CurlSslConfig::apply($ch);

        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        return $ch;
    }

    /**
     * Makes a GET call to retrieve a job.
     * @param string $url URL of the job.
     * @return array<string, integer|float|string|bool|null|array<mixed>> Server response.
     */
    private function sendGetRequest(string $url): array
    {
        /** @var CurlHandle $ch */
        $ch = $this->initChannel();
        curl_setopt($ch, CURLOPT_URL, $url);
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
     * @param InputSource $inputSource File to upload.
     * @param BaseParameters $params Parameters.
     * @return array<string, integer|float|string|bool|null|array<mixed>> Server response.
     * @throws MindeeException Throws if the cURL operation doesn't go succeed.
     */
    private function documentEnqueuePost(
        InputSource $inputSource,
        BaseParameters $params
    ): array {
        $ch = $this->initChannel();
        $postFields = $params->asHash();

        if ($inputSource instanceof UrlInputSource) {
            $postFields['url'] = $inputSource->url;
        } elseif ($inputSource instanceof LocalInputSource) {
            $inputSource->checkNeedsFix();
            $postFields['file'] = $inputSource->fileObject;
        }
        $url = $this->baseUrl . "/v2/products/{$params::$slug}/enqueue";
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

    /**
     * @param array<string, integer|float|string|bool|null|array<mixed>> $result Raw HTTP response array with 'data' and 'code' keys.
     * @throws MindeeV2HttpException Throws if the HTTP status indicates an error.
     * @throws MindeeV2HttpUnknownException Throws if the server sends an unexpected reply.
     */
    private function checkValidResponse(array $result): void
    {
        $statusCode = $result['code'] ?? -1;

        if ($statusCode > 399 || $statusCode < 200) {
            $responseData = json_decode($result['data'], true);

            if ($responseData && isset($responseData['status'])) {
                throw new MindeeV2HttpException(new ErrorResponse($responseData));
            }
            throw new MindeeV2HttpUnknownException(json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    }

    /**
     * Makes a GET call to a search endpoint.
     * @param string $path Search endpoint path (e.g. `/v2/search/models`).
     * @param array<string, string> $queryParams Query parameters to append.
     * @return array<string, integer|float|string|bool|null|array<mixed>> Server response.
     */
    private function reqGetSearch(string $path, array $queryParams): array
    {
        $url = $this->baseUrl . $path;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $ch = $this->initChannel();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);
        return $resp;
    }

    /**
     * Retrieves a list of models matching the given criteria.
     * @param ModelSearchParameters $params Search parameters.
     * @return ModelSearchResponse The list of models matching the criteria.
     */
    public function searchModels(ModelSearchParameters $params): ModelSearchResponse
    {
        return $this->processResponse(
            ModelSearchResponse::class,
            $this->reqGetSearch("/v2/search/models", $params->getQueryParams())
        );
    }

    /**
     * Retrieves a list of RAG documents matching the given criteria.
     * @param RagDocumentSearchParameters $params Search parameters.
     * @return RagDocumentSearchResponse The list of RAG documents matching the criteria.
     */
    public function searchRagDocuments(RagDocumentSearchParameters $params): RagDocumentSearchResponse
    {
        return $this->processResponse(
            RagDocumentSearchResponse::class,
            $this->reqGetSearch("/v2/search/rag-documents", $params->getQueryParams())
        );
    }
}
