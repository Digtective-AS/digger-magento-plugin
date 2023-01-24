<?php

declare(strict_types = 1);

namespace Digtective\Digger\Connectors;

use Digtective\Digger\Helpers\DiggerConfig;
use Laminas\Http\Client;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\Exception\RuntimeException;
use Laminas\Http\Request;
use Psr\Log\LoggerInterface;

class DiggerConnector
{
    /**
     * @var mixed
     */

    private $apiUrl;

    /**
     * @var DiggerConfig
     */
    private $configData;

    /**
     * @var Client
     */

    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @param DiggerConfig $configData
     * @param Client $httpClient
     * @param LoggerInterface $logger
     * @return void
     * @throws Zend_Http_Client_Exception
     */
    public function __construct(
        DiggerConfig $configData,
        Client $httpClient,
        LoggerInterface $logger
    ) {
        $this->configData = $configData;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiUrl = $this->configData->getGeneralConfig("digger_api_url");
    }

    /**
     * Send CreateFormSubmission to backend server
     *
     * @param mixed $sessionId
     * @param mixed $trackingCode
     * @param mixed $referer
     * @param mixed $path
     * @param mixed $userAgent
     * @param mixed $orderStatus
     * @param mixed $orderPrice
     * @return mixed
     */
    public function createFormSubmission(
        $sessionId,
        $trackingCode,
        $referer,
        $path,
        $userAgent,
        $orderStatus,
        $orderPrice
    ) {
        return $this->sendData(
            "$this->apiUrl/api/form-submission",
            \Laminas\Http\Request::METHOD_POST,
            [
                'session_id' => $sessionId,
                'tracking_code' => $trackingCode,
                'referer' => $referer,
                'path' => $path,
                'user_agent' => $userAgent,
                'form' => [
                    'order_status' => $orderStatus,
                    'order_amount' => $orderPrice,
                ]
            ]
        );
    }

    /**
     * Send UpdateFormSubmission to backend server
     *
     * @param mixed $diggerId
     * @param mixed $orderStatus
     * @param mixed $orderPrice
     * @return void
     */
    public function updateFormSubmission(
        $diggerId,
        $orderStatus,
        $orderPrice
    ) {
        $this->sendData(
            "$this->apiUrl/api/form-submission/$diggerId",
            \Laminas\Http\Request::METHOD_PUT,
            [
                'form' => [
                    'order_status' => $orderStatus,
                    'order_amount' => $orderPrice,
                ]
            ]
        );
    }

    /**
     * Send CreatePageView to backend server
     *
     * @param mixed $sessionId
     * @param mixed $trackingCode
     * @param mixed $referer
     * @param mixed $path
     * @param mixed $userAgent
     * @return void
     */
    public function createPageView(
        $sessionId,
        $trackingCode,
        $referer,
        $path,
        $userAgent
    ) {
        $this->sendData(
            "$this->apiUrl/api/page-view",
            \Laminas\Http\Request::METHOD_POST,
            [
                'session_id' => $sessionId,
                'tracking_code' => $trackingCode,
                'referer' => $referer,
                'path' => $path,
                'user_agent' => $userAgent
            ]
        );
    }

    /**
     * Post data to server
     *
     * @param string $endpoint
     * @param string $method
     * @param array $payload
     * @return void
     */
    private function sendData(string $endpoint, string $method, array $payload) : object
    {
        $rc = '{}';
        try {
            $this->httpClient->reset();
            $this->httpClient->setOptions(['timeout' => 60]);
            $request = new Request();
            $request->setUri($endpoint);
            $request->setMethod($method);
            $encodedPayload = \Safe\json_encode($payload);
            $this->setHeaders($request, $encodedPayload);
            $request->setContent($encodedPayload);

            $this->httpClient->send($request);
            $response = $this->httpClient->getResponse();
            if ($response->getStatusCode() !== 200) {
                $this->logger->critical("Digtective : {$response->getReasonPhrase()}");
                $this->logger->critical("Digtective : {$response->getBody()}");
            } else {
                $rc = $response->getBody();
            }
        } catch (\Throwable $t) {
            $this->logger->critical($t);
        }
        return  \Safe\json_decode($rc);
    }

    /**
     * Add common headers to HTTP request
     *
     * @param Request $request
     * @param string $payload
     * @return void
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    private function setHeaders(Request $request, string $payload): void
    {
        $apiToken = $this->configData->getGeneralConfig("digger_api_token");
        $request->getHeaders()->addHeaders([
            'User-Agent' => 'Magento',
            'Cache-Control' => 'no-cache',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($payload),
            'Authorization' => ('Bearer ' . $apiToken),
            'Accept-Encoding' => 'gzip,deflate'
        ]);
    }
}
