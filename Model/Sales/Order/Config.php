<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Sales\Order;

use Imagina\Payulatam\Model\Sales\Order;

class Config extends \Magento\Sales\Model\Order\Config
{
    const XML_PATH_ORDER_STATUS_NEW         = 'payment/orba_payupl/order_status_new';
    const XML_PATH_ORDER_STATUS_HOLDED      = 'payment/orba_payupl/order_status_holded';
    const XML_PATH_ORDER_STATUS_PROCESSING  = 'payment/orba_payupl/order_status_processing';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Sales\Model\Order\StatusFactory $orderStatusFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct(
            $orderStatusFactory,
            $orderStatusCollectionFactory,
            $state
        );
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Gets PayuLatam-specific default status for state.
     *
     * @param string $state
     * @return string
     */
    public function getStateDefaultStatus($state)
    {
        switch ($state) {
            case Order::STATE_PENDING_PAYMENT:
                return $this->scopeConfig->getValue(self::XML_PATH_ORDER_STATUS_NEW, 'store');
            case Order::STATE_HOLDED:
                return $this->scopeConfig->getValue(self::XML_PATH_ORDER_STATUS_HOLDED, 'store');
            case Order::STATE_PROCESSING:
                return $this->scopeConfig->getValue(self::XML_PATH_ORDER_STATUS_PROCESSING, 'store');
        }
        return parent::getStateDefaultStatus($state);
    }
}
