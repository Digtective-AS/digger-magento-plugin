<?php

declare(strict_types = 1);

namespace Digtective\Digger\Model;

use Digtective\Digger\Api\Data\DiggerConsumerRequestInterface;
use Digtective\Digger\Api\Data\DiggerConsumerRequestInterfaceFactory;
use Digtective\Digger\Api\DiggerInterface;
use Digtective\Digger\Connectors\DiggerConnector;
use Digtective\Digger\Consumer\DiggerConsumer;
use Digtective\Digger\Helpers\DiggerConfig;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\HTTP\Header;
use Magento\Framework\MessageQueue\PublisherInterface;

class Digger implements DiggerInterface
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @var Header
     */
    private $httpHeader;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var DiggerConfig
     */
    private $configData;

    /**
     * @var DiggerConnector
     */
    private $diggerConnector;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var DiggerConsumerRequestInterfaceFactory
     */
    private $diggerConsumerRequestFactory;

    /**
     *
     * @param Http $request
     * @param Header $httpHeader
     * @param Session $session
     * @param DiggerConfig $configData
     * @param DiggerConnector $diggerConnector
     * @param PublisherInterface $publisher
     * @param Digtective\Digger\Api\Data\DiggerConsumerRequestInterfaceFactory $diggerConsumerRequestFactory
     * @return void
     */
    public function __construct(
        Http $request,
        Header $httpHeader,
        Session $session,
        DiggerConfig $configData,
        DiggerConnector $diggerConnector,
        PublisherInterface $publisher,
        DiggerConsumerRequestInterfaceFactory $diggerConsumerRequestFactory
    ) {
        $this->request = $request;
        $this->httpHeader = $httpHeader;
        $this->session = $session;
        $this->configData = $configData;
        $this->diggerConnector = $diggerConnector;
        $this->publisher = $publisher;
        $this->diggerConsumerRequestFactory = $diggerConsumerRequestFactory;
    }

    /**
     * @inheritdoc
     */
    public function registerPageView($currentPath, $referer)
    {
        if ($this->stopUntrackableRoutes()) {
            return;
        }

        $sessionId = $this->generateUuid();

        if (!$this->session->getDiggerSessionId()) {
            $this->session->setDiggerSessionId($sessionId);
        } else {
            $sessionId = $this->session->getDiggerSessionId();
        }

        $trackingCode = $this->getTrackingCode($currentPath);

        if ($trackingCode) {
            $this->session->setTrackingCode($trackingCode);
        }

        if (!$this->session->getReferer()) {
            $this->session->setReferer($referer);
        } else {
            $referer = $this->session->getReferer();
        }

        $userAgent = $this->httpHeader->getHttpUserAgent();
        if ($userAgent) {
            $this->session->setUserAgent($userAgent);
        }

        /** @var \Digtective\Digger\Api\Data\DiggerConsumerRequestInterface $requestConsumer */
        $requestConsumer = $this->diggerConsumerRequestFactory->create();
        $requestConsumer->setRequestType(DiggerConsumerRequestInterface::REQUEST_TYPE_PAGE_VIEW);
        $requestConsumer->setDiggerSessionId($sessionId);
        $requestConsumer->setTrackingCode($trackingCode);
        $requestConsumer->setReferer($referer);
        $requestConsumer->setCurrentUrl($currentPath);
        $requestConsumer->setUserAgent($userAgent);
        $this->publisher->publish(DiggerConsumer::DIGGER_REQUEST_TOPIC, $requestConsumer);
    }

    /**
     * Return tracking code from url
     *
     * @param string $url
     * @return string
     */
    private function getTrackingCode(string $url): string
    {
        $trackingCodeIdentifier = $this->configData->getGeneralConfig("digger_tracking_code_identifier");

        $uri = \Laminas\Uri\UriFactory::factory($url);
        $query = $uri->getQueryAsArray();

        if (array_key_exists($trackingCodeIdentifier, $query)) {
            return $query[$trackingCodeIdentifier];
        } else {
            return '';
        }
    }

    /**
     * Prevent logging of Magento backend urls
     *
     * @return bool
     */
    private function stopUntrackableRoutes()
    {
        $moduleName = $this->request->getModuleName();
        if ($moduleName === "admin") {
            return true;
        }

        return false;
    }

    /**
     * Generate a GUID
     *
     * @return string
     */
    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff)
        );
    }
}
