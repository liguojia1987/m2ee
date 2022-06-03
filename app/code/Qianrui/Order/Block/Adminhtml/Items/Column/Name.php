<?php

namespace Qianrui\Order\Block\Adminhtml\Items\Column;

class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    public function getImeis() : array
    {
        return explode(',', $this->getItem()->getData('imei'));
    }

    public function isAllowed($resourceId) : bool
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
