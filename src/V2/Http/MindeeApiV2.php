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
use Mindee\V2\ClientOptions\BaseAnnotationParameters;
use Mindee\V2\ClientOptions\BaseProductParameters;
use Mindee\V2\ClientOptions\BaseSearchParameters;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Error\MindeeV2HttpUnknownException;
use Mindee\V2\Parsing\Error\ErrorResponse;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Mindee\V2\Parsing\BaseRagAnnotationResponse;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Parsing\Search\BaseSearchResponse;
use Mindee\V2\Product\Extraction\RagDocuments\Params\RagDocumentUploadParameters;
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
     * @param BaseProductParameters $params Parameters for the inference.
     * @return JobResponse Server response wrapped in a JobResponse object.
     * @throws MindeeException Throws if the model ID is not provided.
     */
    public function reqPostEnqueue(InputSource $inputDoc, BaseProductParameters $params): JobResponse
    {
        if (!isset($params->modelId)) {
            throw new MindeeException("Model ID must be provided.", ErrorCode::USER_INPUT_ERROR);
        }
        $response = $this->documentEnqueuePost($inputDoc, $params);
        return $this->deserializeResponse(JobResponse::class, $response);
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
    private function deserializeResponse(
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
            throw new MindeeException(
                "Couldn't deserialize response object.",
                ErrorCode::API_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * Requests the job of a queued document from the API.
     * Throws an error if the server's response contains one.
     * @param string $jobId UUID of the job.
     * @return JobResponse Server response wrapped in a JobResponse object.
     * @throws MindeeException Throws if the server's response contains an error.
     * @throws MindeeException Throws if the inference ID is not provided.
     */
    public function reqGetJobById(string $jobId): JobResponse
    {
        return $this->reqGetJobFromUrl($this->baseUrl . "/v2/jobs/$jobId");
    }

    /**
     * Requests the job of a queued document from the API.
     * Throws an error if the server's response contains one.
     * @param string $url URL of the job.
     * @return JobResponse Server response wrapped in a JobResponse object.
     * @throws MindeeException Throws if the server's response contains an error.
     * @throws MindeeException Throws if the inference ID is not provided.
     */
    public function reqGetJobFromUrl(string $url): JobResponse
    {
        $response = $this->sendGetRequest($url);
        return $this->deserializeResponse(JobResponse::class, $response);
    }

    /**
     * @template T of BaseResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $resultId UUID of the result.
     * @return T A response containing parsing results.
     * @throws MindeeException Throws if the server's response contains an error.
     * @throws MindeeApiException Throws if the response class is not valid.
     */
    public function reqGetResultById(
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
        return $this->reqGetResultFromUrl($responseClass, $url);
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
        return $this->deserializeResponse($responseClass, $response);
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
     * @param BaseProductParameters $params Parameters.
     * @return array<string, integer|float|string|bool|null|array<mixed>> Server response.
     * @throws MindeeException Throws if the cURL operation doesn't go succeed.
     */
    private function documentEnqueuePost(
        InputSource $inputSource,
        BaseProductParameters $params
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
     * Uploads a local document to the RAG database.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param LocalInputSource $inputSource Local file to upload.
     * @param RagDocumentUploadParameters $params Upload parameters.
     * @return T
     * @throws MindeeException Throws if the cURL operation fails.
     */
    public function reqPostRagDocument(
        string $responseClass,
        LocalInputSource $inputSource,
        RagDocumentUploadParameters $params
    ): BaseRagAnnotationResponse {
        $ch = $this->initChannel();
        $postFields = $params->getRequestParameters();

        $inputSource->checkNeedsFix();
        $postFields['file'] = $inputSource->fileObject;

        $url = $this->baseUrl . '/v2/products/extraction/rag-documents';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        $curlError = curl_error($ch);
        curl_close($ch);

        if (!empty($curlError)) {
            throw new MindeeException("cURL error:\n$curlError");
        }

        /** @var T $response */
        $response = $this->deserializeResponse($responseClass, $resp);
        return $response;
    }

    /**
     * Makes a PATCH call with a JSON body.
     *
     * @param string $url URL to send the request to.
     * @param array<string, mixed> $body Request body to encode as JSON.
     * @return array<string, integer|float|string|bool|null|array<mixed>> Server response.
     */
    private function sendPatchRequest(string $url, array $body): array
    {
        $ch = $this->initChannel();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * Makes a DELETE call.
     *
     * @param string $url URL to send the request to.
     * @return array<string, integer|float|string|bool|null|array<mixed>> Server response.
     */
    private function sendDeleteRequest(string $url): array
    {
        $ch = $this->initChannel();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * Retrieves a RAG document annotation by its ID.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param string $documentId Unique identifier of the RAG document.
     * @return T
     */
    public function reqGetRagAnnotation(string $responseClass, string $documentId): BaseRagAnnotationResponse
    {
        $url = $this->baseUrl . "/v2/products/extraction/rag-documents/$documentId";
        $response = $this->sendGetRequest($url);
        /** @var T $result */
        $result = $this->deserializeResponse($responseClass, $response);
        return $result;
    }

    /**
     * Updates a RAG document annotation using the provided parameters.
     *
     * @template T of BaseRagAnnotationResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param BaseAnnotationParameters $params Annotation parameters including the document ID and fields to update.
     * @return T
     */
    public function reqPatchRagAnnotation(
        string $responseClass,
        BaseAnnotationParameters $params
    ): BaseRagAnnotationResponse {
        $url = $this->baseUrl . "/v2/products/extraction/rag-documents/{$params->documentId}";
        $response = $this->sendPatchRequest($url, $params->getRequestParameters());
        /** @var T $result */
        $result = $this->deserializeResponse($responseClass, $response);
        return $result;
    }

    /**
     * Deletes a RAG document from the extraction database.
     *
     * @param string $documentId Unique identifier of the RAG document to delete.
     * @return bool True if the deletion was successful (2xx response), false otherwise.
     */
    public function reqDeleteExtractionRagDocument(string $documentId): bool
    {
        $url = $this->baseUrl . "/v2/products/extraction/rag-documents/$documentId";
        $response = $this->sendDeleteRequest($url);
        $statusCode = $response['code'] ?? -1;
        return $statusCode >= 200 && $statusCode < 300;
    }

    /**
     * Makes a GET call to a search endpoint and returns the deserialized response.
     *
     * @template T of BaseSearchResponse
     * @param string $responseClass The response class to construct.
     * @phpstan-param class-string<T> $responseClass
     * @param BaseSearchParameters $params Search parameters (slug and query params derived from this).
     * @return T
     */
    public function reqGetSearch(string $responseClass, BaseSearchParameters $params): BaseResponse
    {
        $queryParams = $params->getQueryParams();
        $url = $this->baseUrl . "/v2/search/" . $params::$slug;
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
        return $this->deserializeResponse($responseClass, $resp);
    }
}
