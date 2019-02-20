<?php

namespace Icyd\Payulatam\Model\Order;

class Validator
{
    /**
     * @var \Icyd\Payulatam\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Icyd\Payulatam\Model\ResourceModel\Transaction $transactionResource,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->transactionResource = $transactionResource;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function validateNoTransactions(\Magento\Sales\Model\Order $order)
    {
        return $this->transactionResource->getLastPayuplOrderIdByOrderId($order->getId()) === false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function validatePaymentMethod(\Magento\Sales\Model\Order $order)
    {
        return $order->getPayment()->getMethod() === \Icyd\Payulatam\Model\Payulatam::CODE;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function validateState(\Magento\Sales\Model\Order $order)
    {
        return !in_array($order->getState(), [
            \Magento\Sales\Model\Order::STATE_CANCELED,
            \Magento\Sales\Model\Order::STATE_CLOSED,
            \Magento\Sales\Model\Order::STATE_COMPLETE
        ]);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function validateCustomer(\Magento\Sales\Model\Order $order)
    {
        return $order->getCustomerId() === $this->customerSession->getCustomerId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function validateNotPaid(\Magento\Sales\Model\Order $order)
    {
        return !$order->getTotalPaid();
    }
}
