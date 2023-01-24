<?php

declare(strict_types=1);

namespace Digtective\Digger\Observers;

use Digtective\Digger\Api\Data\DiggerConsumerRequestInterface;
use Digtective\Digger\Api\Data\DiggerConsumerRequestInterfaceFactory;
use Digtective\Digger\Consumer\DiggerConsumer;
use InvalidArgumentException;
use Magento\Catalog\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class OrderCreateObserver implements ObserverInterface
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var Digtective\Digger\Api\Data\DiggerConsumerRequestInterfaceFactory
     */
    private $diggerConsumerRequestFactory;

    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Digtective\Digger\Api\Data\DiggerConsumerRequestInterfaceFactory $diggerConsumerRequestFactory
     */
    public function __construct(
        PublisherInterface $publisher,
        DiggerConsumerRequestInterfaceFactory $diggerConsumerRequestFactory,
        UrlInterface $urlInterface,
        Session $session,
        LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->diggerConsumerRequestFactory = $diggerConsumerRequestFactory;
        $this->urlInterface = $urlInterface;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * Execute is triggered on the create order event.
     *
     * @throws InvalidArgumentException
     */
    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $diggerId = $order->getData('digger_id');

            $sessionId = $this->session->getDiggerSessionId();
            $currentPath = $this->urlInterface->getCurrentUrl();
            $trackingCode = $this->session->getTrackingCode() ?? '';
            $referrer = $this->session->getReferer() ?? '';
            $userAgent = $this->session->getUserAgent() ?? '';

            /** @var \Digtective\Digger\Api\Data\DiggerConsumerRequestInterface $requestConsumer */
            $requestConsumer = $this->diggerConsumerRequestFactory->create();

            $requestConsumer->setRequestType(
                $diggerId ?
                DiggerConsumerRequestInterface::REQUEST_TYPE_MODIFY_ORDER
                :
                DiggerConsumerRequestInterface::REQUEST_TYPE_CREATE_ORDER
            );

            $requestConsumer->setDiggerId($diggerId);
            $requestConsumer->setOrderEntityId($order->getId());
            $requestConsumer->setDiggerSessionId($sessionId);
            $requestConsumer->setTrackingCode($trackingCode);
            $requestConsumer->setReferer($referrer);
            $requestConsumer->setCurrentUrl($currentPath);
            $requestConsumer->setUserAgent($userAgent);
            $requestConsumer->setOrderStatus($order->getStatus());
            $requestConsumer->setGrandTotal($order->getGrandTotal());
            $this->publisher->publish(DiggerConsumer::DIGGER_REQUEST_TOPIC, $requestConsumer);
        } catch (\Throwable $t) {
            $this->logger->critical($t);
        }
    }
}
