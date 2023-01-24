<?php

namespace Digtective\Digger\Api\Data;

interface DiggerConsumerRequestInterface
{
    public const REQUEST_TYPE_PAGE_VIEW = 1;
    public const REQUEST_TYPE_CREATE_ORDER = 2;
    public const REQUEST_TYPE_MODIFY_ORDER = 3;

    public const REQUEST_TYPE = 'request_type';
    public const DIGGER_ID = 'digger_id';
    public const ORDER_ENTITY_ID = 'order_entity_id';
    public const DIGGER_SESSION_ID = 'digger_session_id';
    public const CURRENT_URL = 'current_url';
    public const TRACKING_CODE = 'tracking_code';
    public const REFERER = 'referer';
    public const USER_AGENT = 'user_agent';
    public const ORDER_STATUS = 'order_status';
    public const GRAND_TOTAL = 'grand_total';

    /**
     * Get Request Type
     *
     * @return int
     */
    public function getRequestType(): int;

    /**
     * Set Request Type
     *
     * @param int $val
     * @return $this
     */
    public function setRequestType(int $val);

    /**
     * Get Digger Id
     *
     * @return string|null
     */
    public function getDiggerId(): ?string;

    /**
     * Set Digger Id
     *
     * @param string|null $val
     * @return $this
     */
    public function setDiggerId(?string $val);

    /**
     * Get Digger Session Id
     *
     * @return string
     */
    public function getDiggerSessionId(): string;

    /**
     * Set Digger Session Id
     *
     * @param string $val
     * @return $this
     */
    public function setDiggerSessionId(string $val);

    /**
     * Get Current Url
     *
     * @return string
     */
    public function getCurrentUrl(): string;

    /**
     * Set Current Url
     *
     * @param string $val
     * @return $this
     */
    public function setCurrentUrl(string $val);

    /**
     * Get Tracking Code
     *
     * @return string
     */
    public function getTrackingCode(): string;

    /**
     * Set Tracking Code
     *
     * @param string $val
     * @return $this
     */
    public function setTrackingCode(string $val);

    /**
     * Get Referer
     *
     * @return string
     */
    public function getReferer(): string;

    /**
     * Set Referer
     *
     * @param string $val
     * @return $this
     */
    public function setReferer(string $val);

    /**
     * Get User Agent
     *
     * @return string
     */
    public function getUserAgent(): string;

    /**
     * Set User Agent
     *
     * @param string $val
     * @return $this
     */
    public function setUserAgent(string $val);

    /**
     * Get Order Entity Id
     *
     * @return string
     */
    public function getOrderEntityId(): ?string;

    /**
     * Set Order Entity Id
     *
     * @param string $val
     * @return $this
     */
    public function setOrderEntityId(?string $val);

    /**
     * Get Order Status
     *
     * @return string
     */
    public function getOrderStatus(): ?string;

    /**
     * Set Order Status
     *
     * @param string $val
     * @return $this
     */
    public function setOrderStatus(?string $val);

    /**
     * Get Grand Total
     *
     * @return float
     */
    public function getGrandTotal(): ?float;

    /**
     * Set Grand Total
     *
     * @param float $val
     * @return $this
     */
    public function setGrandTotal(?float $val);
}
