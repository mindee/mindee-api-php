<?php

declare(strict_types=1);

namespace Mindee\V1\Http;

use CurlHandle;
use Mindee\Http\CurlSslConfig;

/**
 * Abstract class for endpoints.
 */
abstract class BaseEndpoint
{
    /**
     * @param MindeeApi|MindeeWorkflowApi $settings Input settings.
     */
    public function __construct(public MindeeApi|MindeeWorkflowApi $settings) {}

    /**
     * Starts a CURL session using GET.
     *
     * @param string $queueId ID of the queue to poll.
     * @return array{data: string|bool, code: int}
     */
    protected function initCurlSessionGet(string $queueId): array
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );
        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . "/documents/queue/$queueId");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        CurlSslConfig::apply($ch);
        curl_setopt($ch, CURLOPT_USERAGENT, getUserAgent());

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * @param CurlHandle $ch Curl Channel.
     * @param string $suffix Optional suffix for the url call.
     * @param array<string, string|array<mixed>|boolean>|null $postFields Post fields.
     * @param string|null $workflowId Optional ID of the workflow.
     * @return array{data: string|bool, code: int} Final response.
     */
    public function setFinalCurlOpts(
        CurlHandle $ch,
        string $suffix,
        ?array $postFields,
        ?string $workflowId = null
    ): array {
        if (isset($workflowId)) {
            $url = $this->settings->baseUrl . "/v1/workflows/" . $workflowId . $suffix;
        } else {
            $url = $this->settings->urlRoot . $suffix;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($postFields !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        CurlSslConfig::apply($ch);
        curl_setopt($ch, CURLOPT_USERAGENT, getUserAgent());
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
}
