<?php
/**
 * @copyright Copyright (c) 2017 Icyd Colombia (https://www.imaginacolombia.com)
 */

namespace Icyd\Payulatam\Block\Payment\Info;

class Buttons extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'payment/info/buttons.phtml';

    public function getOrderId()
    {
        return $this->getParentBlock()->getInfo()->getOrder()->getId();
    }
}
