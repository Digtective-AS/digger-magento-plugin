<?php

declare(strict_types = 1);

namespace Digtective\Digger\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

class Digger extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     *
     * @var mixed
     */
    protected $diggerConfig;

    /**
     *
     * @param Context $context
     * @param array $data
     * @return void
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Get URL to the Ajax endpoint for register page visits
     *
     * @return string
     */
    public function getRegisterPageViewUrl() : string
    {
        return $this->urlBuilder->getBaseUrl() . 'rest/V1/digger/pageview';
    }
}
