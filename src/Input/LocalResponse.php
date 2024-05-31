<?php

namespace Mindee\Input;

use Mindee\Error\MindeeException;

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
     * @param mixed $inputFile A string, path or file-like object to load as a local response.
     * @throws MindeeException Throws if the input file isn't acceptable.
     */
    public function __construct($inputFile)
    {
        if (is_resource($inputFile) && get_resource_type($inputFile) === 'file') {
            $content = fread($inputFile, filesize($inputFile));
            $strStripped = str_replace(["\r", "\n"], '', $content);
            $this->file = fopen('php://memory', 'r+');
            fwrite($this->file, $strStripped);
            rewind($this->file);
        } elseif (is_resource($inputFile) && get_resource_type($inputFile) === 'stream') {
            $content = stream_get_contents($inputFile);
            $strStripped = str_replace(["\r", "\n"], '', $content);
            $this->file = fopen('php://memory', 'r+');
            fwrite($this->file, $strStripped);
            rewind($this->file);
        } elseif (is_string($inputFile) && file_exists($inputFile)) {
            $content = file_get_contents($inputFile);
            $strStripped = str_replace(["\r", "\n"], '', $content);
            $this->file = fopen('php://memory', 'r+');
            fwrite($this->file, $strStripped);
            rewind($this->file);
        } elseif (is_string($inputFile)) {
            $strStripped = str_replace(["\r", "\n"], '', $inputFile);
            $this->file = fopen('php://memory', 'r+');
            fwrite($this->file, $strStripped);
            rewind($this->file);
        } elseif (is_string($inputFile) || is_array($inputFile)) {
            $strStripped = str_replace(["\r", "\n"], '', $inputFile);
            $this->file = fopen('php://memory', 'r+');
            fwrite($this->file, $strStripped);
            rewind($this->file);
        } else {
            throw new MindeeException("Incompatible type for input.");
        }
    }

    /**
     * @return array
     * @throws MindeeException Throws if the file contents cannot be converted to a valid array.
     */
    public function toArray(): array
    {
        try {
            rewind($this->file);
            $content = stream_get_contents($this->file);
            $json = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new MindeeException("File is not a valid dictionary.");
            }
            return $json;
        } catch (MindeeException $e) {
            throw new MindeeException("File is not a valid dictionary.", 0, $e);
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
            throw new MindeeException("Could not get HMAC signature from payload.", 0, $e);
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
}
