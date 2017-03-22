<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Processor
{
    /**
     * @var \Imagina\Payulatam\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Imagina\Payulatam\Model\Transaction\Service
     */
    protected $transactionService;

    /**
     * @param \Imagina\Payulatam\Model\Order $orderHelper
     * @param \Imagina\Payulatam\Model\Transaction\Service $transactionService
     */
    public function __construct(
        \Imagina\Payulatam\Model\Order $orderHelper,
        \Imagina\Payulatam\Model\Transaction\Service $transactionService
    ) {
        $this->orderHelper = $orderHelper;
        $this->transactionService = $transactionService;
    }

    /**
     * @param string $payuplOrderId
     * @param string$status
     * @param bool $close
     * @throws LocalizedException
     */
    public function processOld($payuplOrderId, $status, $close = false)
    {
        $this->transactionService->updateStatus($payuplOrderId, $status, $close);
    }

    /**
     * @param string $payuplOrderId
     * @param string $status
     * @throws LocalizedException
     */
    public function processPending($payuplOrderId, $status)
    {
        $this->transactionService->updateStatus($payuplOrderId, $status);
    }

    /**
     * @param string $payuplOrderId
     * @param string $status
     * @throws LocalizedException
     */
    public function processHolded($payuplOrderId, $status)
    {
        $order = $this->loadOrderByPayuplOrderId($payuplOrderId);
        $this->orderHelper->setHoldedOrderStatus($order, $status);
        $this->transactionService->updateStatus($payuplOrderId, $status, true);
    }

    /**
     * @param string $payuplOrderId
     * @param string $status
     * @throws LocalizedException
     * @todo Implement some additional logic for transaction confirmation by store owner.
     */
    public function processWaiting($payuplOrderId, $status)
    {
        $this->transactionService->updateStatus($payuplOrderId, $status);
    }

    /**
     * @param string $payuplOrderId
     * @param string $status
     * @param float $amount
     * @throws LocalizedException
     */
    public function processCompleted($payuplOrderId, $status, $amount)
    {
        $order = $this->loadOrderByPayuplOrderId($payuplOrderId);
        $this->orderHelper->completePayment($order, $amount, $payuplOrderId);
        $this->transactionService->updateStatus($payuplOrderId, $status, true);
    }

    /**
     * @param string $payuplOrderId
     * @return \Imagina\Payulatam\Model\Sales\Order
     * @throws LocalizedException
     */
    protected function loadOrderByPayuplOrderId($payuplOrderId)
    {
        $order = $this->orderHelper->loadOrderByPayuplOrderId($payuplOrderId);
        if (!$order) {
            throw new LocalizedException(new Phrase('Order not found.'));
        }
        return $order;
    }
}
