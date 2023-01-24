<?php

namespace Digtective\Digger\Model\Data;

use Digtective\Digger\Api\Data\DiggerConsumerRequestInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class DiggerConsumerRequest extends AbstractSimpleObject implements DiggerConsumerRequestInterface
{
    /**
     * @inheritdoc
     */
    public function getRequestType(): int
    {
        return $this->_get(self::REQUEST_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setRequestType(int $val)
    {
        return $this->setData(self::REQUEST_TYPE, $val);
    }

    /**
     * @inheritdoc
     */
    public function getDiggerId(): ?string
    {
        return $this->_get(self::DIGGER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setDiggerId(?string $val)
    {
        return $this->setData(self::DIGGER_ID, $val);
    }

    /**
     * @inheritdoc
     */
    public function getDiggerSessionId(): string
    {
        return $this->_get(self::DIGGER_SESSION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setDiggerSessionId(string $val)
    {
        return $this->setData(self::DIGGER_SESSION_ID, $val);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentUrl(): string
    {
        return $this->_get(self::CURRENT_URL);
    }

    /**
     * @inheritdoc
     */
    public function setCurrentUrl(string $val)
    {
        return $this->setData(self::CURRENT_URL, $val);
    }

    /**
     * @inheritdoc
     */
    public function getTrackingCode(): string
    {
        return $this->_get(self::TRACKING_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setTrackingCode(string $val)
    {
        return $this->setData(self::TRACKING_CODE, $val);
    }

    /**
     * @inheritdoc
     */
    public function getReferer(): string
    {
        return $this->_get(self::REFERER);
    }

    /**
     * @inheritdoc
     */
    public function setReferer(string $val)
    {
        return $this->setData(self::REFERER, $val);
    }

    /**
     * @inheritdoc
     */
    public function getUserAgent(): string
    {
        return $this->_get(self::USER_AGENT);
    }

    /**
     * @inheritdoc
     */
    public function setUserAgent(string $val)
    {
        return $this->setData(self::USER_AGENT, $val);
    }

    /**
     * @inheritdoc
     */
    public function getOrderEntityId(): ?string
    {
        return $this->_get(self::ORDER_ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderEntityId(?string $val)
    {
        return $this->setData(self::ORDER_ENTITY_ID, $val);
    }

    /**
     * @inheritdoc
     */
    public function getOrderStatus(): ?string
    {
        return $this->_get(self::ORDER_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setOrderStatus(?string $val)
    {
        return $this->setData(self::ORDER_STATUS, $val);
    }

    /**
     * @inheritdoc
     */
    public function getGrandTotal(): ?float
    {
        return $this->_get(self::GRAND_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setGrandTotal(?float $val)
    {
        return $this->setData(self::GRAND_TOTAL, $val);
    }
}
