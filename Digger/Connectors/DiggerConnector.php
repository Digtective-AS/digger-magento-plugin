<?php

namespace Digtective\Digger\Connectors;

use Digtective\Digger\Helpers\DiggerConfig;
use Magento\Framework\HTTP\ZendClient;

class DiggerConnector
{
    private $apiUrl;
    private $configData;
    private $httpClient;

    public function __construct(
        DiggerConfig $configData,
        ZendClient $curl
    ) {
        $this->configData = $configData;
        $this->httpClient = $curl;
        $this->apiUrl = $this->configData->getGeneralConfig("digger_api_url");
        $this->setupHttpClient();
    }

    private function setupHttpClient()
    {
        $apiToken = $this->configData->getGeneralConfig("digger_api_token");

        $this->httpClient->setHeaders([
            "Contet-Type: application/json",
            "Accept: application/json",
            "Authorization: Bearer $apiToken"
        ]);
    }

    public function createFormSubmission(
        $sessionId,
        $trackingCode,
        $referer,
        $path,
        $userAgent,
        $orderStatus,
        $orderPrice
    ) {
        $this->httpClient->setUri("$this->apiUrl/api/form-submission");
        $this->httpClient->setMethod(\Zend_Http_Client::POST);
        $this->setRequestData([
            'session_id' => $sessionId,
            'tracking_code' => $trackingCode,
            'referer' => $referer,
            'path' => $path,
            'user_agent' => $userAgent,
            'form' => [
                'order_status' => $orderStatus,
                'order_amount' => $orderPrice,
            ]
        ]);

        return \Safe\json_decode($this->httpClient->request()->getBody());
    }

    public function updateFormSubmission(
        $diggerId,
        $orderStatus,
        $orderPrice
    ) {
        $this->httpClient->setUri("$this->apiUrl/api/form-submission/$diggerId");
        $this->httpClient->setMethod(\Zend_Http_Client::PUT);
        $this->setRequestData([
            'form' => [
                'order_status' => $orderStatus,
                'order_amount' => $orderPrice,
            ]
        ]);

        $this->httpClient->request();
    }

    public function createPageView(
        $sessionId,
        $trackingCode,
        $referer,
        $path,
        $userAgent
    ) {
        $this->httpClient->setUri("$this->apiUrl/api/page-view");
        $this->httpClient->setMethod(\Zend_Http_Client::POST);
        $this->setRequestData([
            'session_id' => $sessionId,
            'tracking_code' => $trackingCode,
            'referer' => $referer,
            'path' => $path,
            'user_agent' => $userAgent
        ]);
        $this->httpClient->request();
    }

    private function setRequestData($requestData)
    {
        $this->httpClient->setRawData(\Safe\json_encode($requestData), 'application/json');
    }
}
