<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\URLInputSource;

/**
 * Endpoint management.
 */
class Endpoint extends BaseEndpoint
{
    /**
     * @var string Url (name) of then endpoint.
     */
    public string $urlName;
    /**
     * @var string Name of the endpoint's owner.
     */
    public string $owner;
    /**
     * @var string Version of the endpoint.
     */
    public string $version;

    /**
     * @param string    $urlName  Url (name) of the endpoint.
     * @param string    $owner    Name of the endpoint's owner.
     * @param string    $version  Version of the endpoint.
     * @param MindeeApi $settings Settings for the endpoint.
     */
    public function __construct(
        string $urlName,
        string $owner,
        string $version,
        MindeeApi $settings
    ) {
        parent::__construct($settings);
        $this->urlName = $urlName;
        $this->owner = $owner;
        $this->version = $version;
    }

    /**
     * Retrieves a document from its queue ID.
     *
     * @param string $queueId ID of the queue to poll.
     * @return array
     */
    public function documentQueueReqGet(string $queueId): array
    {
        return $this->initCurlSessionGet($queueId);
    }

    /**
     * Sends a document for asynchronous enqueuing.
     *
     * @param InputSource          $fileCurl File to upload.
     * @param PredictMethodOptions $options  Prediction Options.
     * @return array
     */
    public function predictRequestPost(
        InputSource $fileCurl,
        PredictMethodOptions $options
    ): array {
        return $this->initCurlSessionPost($fileCurl, $options, false);
    }

    /**
     * Sends a document for synchronous enqueuing.
     *
     * @param InputSource          $fileCurl File to upload.
     * @param PredictMethodOptions $options  Prediction Options.
     * @return array
     */
    public function predictAsyncRequestPost(
        InputSource $fileCurl,
        PredictMethodOptions $options
    ): array {
        return $this->initCurlSessionPost(
            $fileCurl,
            $options,
            true
        );
    }


    /**
     * Starts a CURL session, using POST.
     *
     * @param InputSource          $inputSource File to upload.
     * @param PredictMethodOptions $options     Prediction Options.
     * @param boolean              $async       Whether to use the async endpoint.
     * @return array
     */
    private function initCurlSessionPost(
        InputSource $inputSource,
        PredictMethodOptions $options,
        bool $async
    ): array {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        $suffix = $async ? '/predict_async' : '/predict';
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        $postFields = null;
        if ($inputSource instanceof URLInputSource) {
            $postFields = ['document' => $inputSource->url];
        } elseif ($inputSource instanceof LocalInputSource) {
            $inputSource->checkNeedsFix();
            $postFields = ['document' => $inputSource->fileObject];
        }
        if ($options->predictOptions->includeWords) {
            $postFields['include_mvision'] = 'true';
        }

        if ($options->predictOptions->fullText) {
            $params['full_text_ocr'] = 'true';
        }

        if ($options->rag) {
            $params['rag'] = 'true';
        }

        if ($options->predictOptions->cropper) {
            $params['cropper'] = 'true';
        }

        if (!empty($params)) {
            $suffix .= '?' . http_build_query($params);
        }
        return $this->setFinalCurlOpts($ch, $suffix, $postFields, $options->workflowId);
    }
}
