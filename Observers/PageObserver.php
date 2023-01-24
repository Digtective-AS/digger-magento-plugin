<?php

namespace Digtective\Digger\Observers;

use Digtective\Digger\Connectors\DiggerConnector;
use Digtective\Digger\Helpers\DiggerConfig;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\UrlInterface;

class PageObserver implements ObserverInterface
{
    private $request;
    private $urlInterface;
    private $httpHeader;
    private $session;
    private $configData;
    private $diggerConnector;

    public function __construct(
        Http $request,
        UrlInterface $urlInterface,
        Header $httpHeader,
        Session $session,
        DiggerConfig $configData,
        DiggerConnector $diggerConnector
    ) {
        $this->request = $request;
        $this->urlInterface = $urlInterface;
        $this->httpHeader = $httpHeader;
        $this->session = $session;
        $this->configData = $configData;
        $this->diggerConnector = $diggerConnector;
    }

    public function execute(Observer $observer)
    {
        if ($this->stopUntrackableRoutes()) {
            return;
        }

        $trackingCodeIdentifier = $this->configData->getGeneralConfig("digger_tracking_code_identifier");

        $sessionId = $this->generateUuid();
        $currentPath = $this->urlInterface->getCurrentUrl();

        if (!$this->session->getDiggerSessionId()) {
            $this->session->setDiggerSessionId($sessionId);
        } else {
            $sessionId = $this->session->getDiggerSessionId();
        }

        $trackingCode = $this->request->getParam($trackingCodeIdentifier);
        if ($trackingCode) {
            $this->session->setTrackingCode($trackingCode);
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if (!$this->session->getReferer()) {
            $this->session->setReferer($referer);
        } else {
            $referer = $this->session->getReferer();
        }

        $userAgent = $this->httpHeader->getHttpUserAgent();
        if ($userAgent) {
            $this->session->setUserAgent($userAgent);
        }

        $this->diggerConnector->createPageView(
            $sessionId,
            $trackingCode,
            $referer,
            $currentPath,
            $userAgent
        );
    }

    private function stopUntrackableRoutes()
    {
        $moduleName = $this->request->getModuleName();
        if ($moduleName === "admin") {
            return true;
        }

        return false;
    }

    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
