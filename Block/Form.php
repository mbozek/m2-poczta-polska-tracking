<?php

namespace PocztaPolska\Tracking\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('tracking/index/tracking', ['_secure' => true]);
    }
}
