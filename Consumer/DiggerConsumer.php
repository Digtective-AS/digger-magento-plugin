<?php

declare(strict_types = 1);

namespace Digtective\Digger\Consumer;

use Digtective\Digger\Api\Data\DiggerConsumerRequestInterface;
use Digtective\Digger\Connectors\DiggerConnector;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

use Psr\Log\LoggerInterface;

class DiggerConsumer
{
    public const DIGGER_REQUEST_TOPIC = 'digger.request';

    /**
     * @var DiggerConnector
     */
    private $diggerConnector;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @param DiggerConnector $diggerConnector
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     * @return void
     */
    public function __construct(
        DiggerConnector $diggerConnector,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->diggerConnector = $diggerConnector;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * Process queue
     *
     * @param DiggerConsumerRequestInterface $diggerConsumerRequest
     * @throws LocalizedException
     */
    public function process(DiggerConsumerRequestInterface $diggerConsumerRequest)
    {
        switch ($diggerConsumerRequest->getRequestType()) {
            case DiggerConsumerRequestInterface::REQUEST_TYPE_CREATE_ORDER:
                $orderId = $diggerConsumerRequest->getOrderEntityId();
                $result = $this->diggerConnector->createFormSubmission(
                    $diggerConsumerRequest->getDiggerSessionId(),
                    $diggerConsumerRequest->getTrackingCode(),
                    $diggerConsumerRequest->getReferer(),
                    $diggerConsumerRequest->getCurrentUrl(),
                    $diggerConsumerRequest->getUserAgent(),
                    $diggerConsumerRequest->getOrderStatus(),
                    $diggerConsumerRequest->getGrandTotal()
                );

                if ($result) {
                    try {
                        $adapter = $this->resourceConnection->getConnection();
                        $adapter->update(
                            $adapter->getTableName('sales_order'),
                            [
                                'digger_id' => $result->id
                            ],
                            "entity_id = {$orderId}"
                        );
                    } catch (\Throwable $t) {
                        $this->logger->critical("DIGGER: Cannot update order with id : {$orderId}");
                    }
                }
                break;
            case DiggerConsumerRequestInterface::REQUEST_TYPE_MODIFY_ORDER:
                $this->diggerConnector->updateFormSubmission(
                    $diggerConsumerRequest->getDiggerId(),
                    $diggerConsumerRequest->getOrderStatus(),
                    $diggerConsumerRequest->getGrandTotal()
                );
                break;
            case DiggerConsumerRequestInterface::REQUEST_TYPE_PAGE_VIEW:
                $this->diggerConnector->createPageView(
                    $diggerConsumerRequest->getDiggerSessionId(),
                    $diggerConsumerRequest->getTrackingCode(),
                    $diggerConsumerRequest->getReferer(),
                    $diggerConsumerRequest->getCurrentUrl(),
                    $diggerConsumerRequest->getUserAgent()
                );
                break;
        }
    }
}
