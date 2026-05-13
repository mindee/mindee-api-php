<?php

declare(strict_types=1);

namespace Mindee\Input;

use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeException;

use function is_array;
use function is_resource;
use function is_string;

/**
 * Local response loaded from a file.
 */
class LocalResponse
{
    /**
     * @var mixed $file File object of the local response.
     */
    private $file;

    /**
     * @param resource|string|array<string> $inputFile A string, path or file-like object to load as a local response.
     * @throws MindeeException Throws if the input file isn't acceptable.
     */
    public function __construct(mixed $inputFile)
    {
        if (is_resource($inputFile)) {
            $resourceType = get_resource_type($inputFile);
            if ($resourceType === 'file') {
                $content = fread($inputFile, fstat($inputFile)['size']);
            } elseif ($resourceType === 'stream') {
                $content = stream_get_contents($inputFile);
            } else {
                throw new MindeeException("Unsupported resource type.", ErrorCode::USER_INPUT_ERROR);
            }
        } elseif (is_string($inputFile)) {
            if (file_exists($inputFile) && is_file($inputFile)) {
                $content = file_get_contents($inputFile);
            } else {
                $content = $inputFile;
            }
        } elseif (is_array($inputFile)) {
            $content = implode('', $inputFile);
        } else {
            throw new MindeeException("Incompatible type for input.", ErrorCode::USER_INPUT_ERROR);
        }

        $strStripped = str_replace(["\r", "\n"], '', (string) $content);
        $this->file = fopen('php://memory', 'r+');
        fwrite($this->file, $strStripped);
        rewind($this->file);
    }

    /**
     * @throws MindeeException Throws if the file contents cannot be converted to a valid array.
     * @return array<string, mixed> The file contents.
     */
    public function toArray(): array
    {
        try {
            rewind($this->file);
            $content = stream_get_contents($this->file);
            $json = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new MindeeException(
                    "File is not a valid JSON-like object.",
                    ErrorCode::USER_INPUT_ERROR
                );
            }
            return $json;
        } catch (MindeeException $e) {
            throw new MindeeException(
                "File is not a valid dictionary.",
                ErrorCode::USER_INPUT_ERROR,
                $e
            );
        }
    }

    /**
     * @param string $secretKey Secret key as a string.
     * @return string a valid HMAC signature
     * @throws MindeeException Throws when either the file is unreadable, or when the secret is invalid.
     */
    public function getHMACSignature(string $secretKey): string
    {
        $algorithm = 'sha256';

        try {
            rewind($this->file);
            $content = stream_get_contents($this->file);
            return hash_hmac($algorithm, $content, $secretKey);
        } catch (MindeeException $e) {
            throw new MindeeException(
                "Could not get HMAC signature from payload.",
                ErrorCode::FILE_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * @param string $secretKey Secret, given key as a string.
     * @param string $signature HMAC signature as a string.
     * @return boolean
     */
    public function isValidHMACSignature(string $secretKey, string $signature): bool
    {
        return $signature === $this->getHMACSignature($secretKey);
    }

    /**
     * Deserialize the loaded local response into the requested BaseResponse-derived class.
     *
     * Typically used when dealing with V2 webhook callbacks.
     *
     * @param string $responseClass The class name into which the payload should be deserialized.
     * @return mixed An instance of responseClass populated with the file content.
     * @throws MindeeException If the provided class cannot be instantiated.
     */
    public function deserializeResponse(string $responseClass): mixed
    {
        try {
            $data = $this->toArray();
            return new $responseClass($data);
        } catch (Exception $e) {
            throw new MindeeException(
                "Invalid class specified for deserialization: " . $e->getMessage(),
                ErrorCode::INTERNAL_LIBRARY_ERROR,
                $e
            );
        }
    }
}
