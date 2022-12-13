<?php

namespace Digtective\Digger\Observers;

use Digtective\Digger\Connectors\DiggerConnector;
use Magento\Catalog\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class OrderCreateObserver implements ObserverInterface
{
    private $urlInterface;
    private $session;
    private $diggerConnector;

    public function __construct(
        UrlInterface $urlInterface,
        Session $session,
        DiggerConnector $diggerConnector,
    ) {
        $this->urlInterface = $urlInterface;
        $this->session = $session;
        $this->diggerConnector = $diggerConnector;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $diggerId = $order->getData('digger_id');

        if ($diggerId) {
            $this->updateFormSubmission($order);
        } else {
            $this->createFormSubmission($order);
        }
    }

    private function createFormSubmission($order)
    {
        $sessionId = $this->session->getDiggerSessionId();
        $currentPath = $this->urlInterface->getCurrentUrl();
        $trackingCode = $this->session->getTrackingCode();
        $referrer = $this->session->getReferer();
        $userAgent = $this->session->getUserAgent();

        $result = $this->diggerConnector->createFormSubmission(
            $sessionId,
            $trackingCode,
            $referrer,
            $currentPath,
            $userAgent,
            $order->getStatus(),
            $order->getGrandTotal()
        );

        $order->setData('digger_id', $result->id);
        $order->save();
    }

    private function updateFormSubmission($order)
    {
        $this->diggerConnector->updateFormSubmission(
            $order->getData('digger_id'),
            $order->getStatus(),
            $order->getGrandTotal()
        );
    }
}
