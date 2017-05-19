<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model;

class Order
{
    /**
     * @var ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var Sales\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    protected $checkoutSuccessValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var Order\Validator
     */
    protected $orderValidator;

    /**
     * @param ResourceModel\Transaction $transactionResource
     * @param Sales\OrderFactory $orderFactory
     * @param \Magento\Checkout\Model\Session\SuccessValidator $checkoutSuccessValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Order\Validator $orderValidator
     */
    public function __construct(
        ResourceModel\Transaction $transactionResource,
        Sales\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session\SuccessValidator $checkoutSuccessValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        Order\Validator $orderValidator
    ) {
        $this->transactionResource = $transactionResource;
        $this->orderFactory = $orderFactory;
        $this->checkoutSuccessValidator = $checkoutSuccessValidator;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->orderValidator = $orderValidator;
    }

    /**
     * Saves new order transaction incrementing "try".
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $payulatamOrderId
     * @param string $payuplExternalOrderId
     * @param string $status
     */
    public function addNewOrderTransaction(
        \Magento\Sales\Model\Order $order,
        $payulatamOrderId,
        $payuplExternalOrderId,
        $status
    ) {

        $orderId = $order->getId();
        $payment = $order->getPayment();
        $payment->setTransactionId($payulatamOrderId);
        $payment->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, [
            'order_id' => $payuplExternalOrderId,
            'try' => $this->transactionResource->getLastTryByOrderId($orderId) + 1,
            'status' => $status
        ]);
        $payment->setIsTransactionClosed(0);
        $transaction = $payment->addTransaction('order');
        $transaction->save();
        $payment->save();
    }

    /**
     * @param int $orderId
     * @return Sales\Order|false
     */
    public function loadOrderById($orderId)
    {
        /**
         * @var $order Sales\Order
         */
        $order = $this->orderFactory->create();
        $order->load($orderId);
        if ($order->getId()) {
            return $order;
        }
        return false;
    }

    /**
     * @param string $payulatamOrderId
     * @return Sales\Order|false
     */
    public function loadOrderByPayuplOrderId($payulatamOrderId)
    {
        $orderId = $this->transactionResource->getOrderIdByPayuplOrderId($payulatamOrderId);
        if ($orderId) {
            return $this->loadOrderById($orderId);
        }
        return false;
    }

    /**
     * @param Sales\Order $order
     */
    public function setNewOrderStatus(Sales\Order $order)
    {
        $order
            ->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT)
            ->addStatusToHistory(true)
            ->save();
    }

    /**
     * @param Sales\Order $order
     * @param string $status
     */
    public function setHoldedOrderStatus(Sales\Order $order, $status)
    {
        $orderState = Sales\Order::STATE_HOLDED;
        $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
        $order
            ->setHoldBeforeState($order->getState())
            ->setHoldBeforeStatus($order->getStatus())
            ->setState($orderState)
            ->setStatus($orderStatus);
        $order->addStatusHistoryComment(__('Payulatam status') . ': ' . $status);
        $order->save();
    }

    /**
     * Registers payment, creates invoice and changes order statatus.
     *
     * @param Sales\Order $order
     * @param float $amount
     */
    public function completePayment(Sales\Order $order, $amount, $payulatamOrderId)
    {
        $payment = $order->getPayment();
        $payment
            ->setParentTransactionId($payulatamOrderId)
            ->setTransactionId($payulatamOrderId . ':C')
            ->registerCaptureNotification($amount)
            ->save();
        foreach ($order->getRelatedObjects() as $object) {
            $object->save();
        }
        $order->save();
    }

    /**
     * @return int|false
     */
    public function getOrderIdForPaymentStart()
    {
        if ($this->checkoutSuccessValidator->isValid()) {
            return $this->checkoutSession->getLastOrderId();
        }
        $orderId = $this->request->getParam('id');
        if ($orderId) {
            return $orderId;
        }
        return false;
    }

    /**
     * Checks if first payment can be started.
     *
     * Order should belong to current logged in customer.
     * Order should have PayuLatam payment method.
     * Order should have no PayuLatam transactions.
     * Order shouldn't be cancelled, closed or completed.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function canStartFirstPayment(\Magento\Sales\Model\Order $order)
    {
        return
            $this->orderValidator->validateCustomer($order) &&
            $this->orderValidator->validateNoTransactions($order) &&
            $this->orderValidator->validatePaymentMethod($order) &&
            $this->orderValidator->validateState($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function canRepeatPayment(\Magento\Sales\Model\Order $order)
    {
        return
            $this->orderValidator->validateCustomer($order) &&
            $this->orderValidator->validatePaymentMethod($order) &&
            $this->orderValidator->validateState($order) &&
            $this->orderValidator->validateNotPaid($order) &&
            !$this->orderValidator->validateNoTransactions($order);
    }

    /**
     * @return bool
     */
    public function paymentSuccessCheck()
    {
        return is_null($this->request->getParam('exception'));
    }
}
