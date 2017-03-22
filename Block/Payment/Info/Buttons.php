<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Block\Payment\Info;

class Buttons extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'payment/info/buttons.phtml';

    public function getOrderId()
    {
        return $this->getParentBlock()->getInfo()->getOrder()->getId();
    }
}
