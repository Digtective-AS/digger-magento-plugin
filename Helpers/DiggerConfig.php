<?php

declare(strict_types = 1);

namespace Digtective\Digger\Helpers;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class DiggerConfig extends AbstractHelper
{
    private const DIGGER_INTEGRATION_PATH = 'digger_integration/';

    /**
     * Retreive a value from Magento core config settings
     *
     * @param mixed $field
     * @return mixed
     */
    public function getConfigValue($field)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get base path for settings
     *
     * @param mixed $code
     * @return mixed
     */
    public function getGeneralConfig($code)
    {
        return $this->getConfigValue(self::DIGGER_INTEGRATION_PATH . 'general/' . $code);
    }
}
