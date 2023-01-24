<?php

namespace Digtective\Digger\Helpers;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class DiggerConfig extends AbstractHelper
{
    const DIGGER_INTEGRATION_PATH = 'digger_integration/';

    public function getConfigValue($field)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getGeneralConfig($code)
    {
        return $this->getConfigValue(self::DIGGER_INTEGRATION_PATH .'general/'. $code);
    }
}
