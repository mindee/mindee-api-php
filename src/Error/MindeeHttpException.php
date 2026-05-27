<?php

declare(strict_types=1);

/**
 * @file
 * Mindee HTTP Exceptions.
 */

namespace Mindee\Error;

use function array_key_exists;
use function is_array;
use function is_string;

/**
 * Exceptions relating to HTTP calls.
 *
 * Handles uncaught error codes.
 */
class MindeeHttpException extends MindeeException
{
    /**
     * @var string|null API code as sent by the server.
     */
    public ?string $apiCode;
    /**
     * @var string|array<string, int|float|string|bool|null|array<array-key, mixed>>|null API details field as sent by the server.
     */
    public string|array|null $apiDetails;
    /**
     * @var string|array<string, int|float|string|bool|null|array<array-key, mixed>>|null API message field as sent by the server.
     */
    public string|array|null $apiMessage;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $httpError Array containing the error data.
     * @param string $url Remote URL the error was found on.
     * @param integer $statusCode Error code.
     */
    public function __construct(array $httpError, string $url, public int $statusCode)
    {
        if (array_key_exists('code', $httpError)) {
            $this->apiCode = $httpError['code'];
        } else {
            $this->apiCode = null;
        }
        if (array_key_exists('details', $httpError)) {
            $this->apiDetails = $httpError['details'];
        } else {
            $this->apiDetails = null;
        }
        if (array_key_exists('message', $httpError)) {
            $this->apiMessage = $httpError['message'];
        } else {
            $this->apiMessage = null;
        }
        if (is_array($this->apiDetails)) {
            $details = "\n" . json_encode($this->apiDetails, JSON_PRETTY_PRINT) . "\n";
        } else {
            $details = (string) ($this->apiDetails);
        }
        parent::__construct("$url $this->statusCode HTTP error: $details - $this->apiMessage");
    }

    /**
     * Builds an appropriate error object from the server reply.
     *
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>>|string|null $response Parsed server response.
     * @return string[]
     * @throws MindeeException Throws if the error itself can't be built.
     */
    public static function createErrorObj(array|string|null $response): array
    {
        if (is_string($response)) {
            if (str_contains($response, 'Maximum pdf pages')) {
                $errorArray = [
                    'code' => 'TooManyPages',
                    'message' => 'Maximum amount of pdf pages reached.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Max file size is')) {
                $errorArray = [
                    'code' => 'FileTooLarge',
                    'message' => 'Maximum file size reached.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Invalid file type')) {
                $errorArray = [
                    'code' => 'InvalidFiletype',
                    'message' => 'Invalid file type.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Gateway timeout')) {
                $errorArray = [
                    'code' => 'RequestTimeout',
                    'message' => 'Request timed out.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Too Many Requests')) {
                $errorArray = [
                    'code' => 'TooManyRequests',
                    'message' => 'Too Many Requests.',
                    'details' => $response,
                ];
            } else {
                $errorArray = [
                    'code' => 'UnknownError',
                    'message' => 'Server sent back an unexpected reply.',
                    'details' => $response,
                ];
            }

            return $errorArray;
        }
        if (
            is_array($response)
            && array_key_exists('api_request', $response)
            && array_key_exists('error', $response['api_request'])
        ) {
            return $response['api_request']['error'];
        }
        if (!isset($response)) {
            throw new MindeeException(
                "Request to the API failed.",
                ErrorCode::API_REQUEST_FAILED
            );
        }
        throw new MindeeException(
            'Could not build a specific HTTP exception from: ' . json_encode($response, JSON_PRETTY_PRINT),
            ErrorCode::EXCEPTION_BUILD_FAILED
        );
    }

    /**
     * @param string $url Remote URL the error was found on.
     * @param array<string, mixed>|string|null $response Raw server response.
     */
    public static function handleError(string $url, array|string|null $response): self
    {
        if (is_array($response)) {
            $dataResponse = $response['data'] ?? ["data" => null];
        } else {
            $dataResponse = ["data" => null];
        }
        $errorObj = self::createErrorObj($dataResponse);
        if (array_key_exists("code", $response) && is_numeric($response['code'])) {
            $code = (int) ($response['code']);
        } else {
            $code = 500;
        }
        if ($code >= 400 && $code <= 499) {
            return new MindeeHttpClientException($errorObj, $url, $code);
        }
        if ($code >= 500 && $code <= 599) {
            return new MindeeHttpClientException($errorObj, $url, $code);
        }

        return new self($errorObj, $url, $code);
    }
}
