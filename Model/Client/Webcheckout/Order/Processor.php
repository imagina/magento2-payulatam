<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Processor
{
    /**
     * @var \Icyd\Payulatam\Model\Order\Processor
     */
    protected $orderProcessor;

    public function __construct(
        \Icyd\Payulatam\Model\Order\Processor $orderProcessor
    ) {
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @param string $payulatamOrderId
     * @param string $status
     * @param float $amount
     * @param bool $newest
     * @return bool
     * @throws LocalizedException
     */
    public function processStatusChange($payulatamOrderId, $status = '', $amount = null, $newest = true)
    {
        if (!in_array($status, [
            Order::STATUS_PRE_NEW,
            Order::STATUS_NEW,
            Order::STATUS_APPROVED,
            Order::STATUS_DECLINED,
            Order::STATUS_ERROR,
            Order::STATUS_EXPIRED,
            Order::STATUS_PENDING
        ])
        ) {
            throw new LocalizedException(new Phrase('Invalid status.'));
        }
        if (!$newest) {
            $close = in_array($status, [
                Order::STATUS_DECLINED,
                Order::STATUS_EXPIRED,
                Order::STATUS_APPROVED
            ]);
            $this->orderProcessor->processOld($payulatamOrderId, $status, $close);
            return true;
        }
        switch ($status) {
            case Order::STATUS_NEW:
            case Order::STATUS_PENDING:
                $this->orderProcessor->processPending($payulatamOrderId, $status);
                return true;
            case Order::STATUS_DECLINED:
            case Order::STATUS_EXPIRED:
            case Order::STATUS_ERROR:
                $this->orderProcessor->processHolded($payulatamOrderId, $status);
                return true;
            case Order::STATUS_APPROVED:
                $this->orderProcessor->processCompleted($payulatamOrderId, $status, $amount);
                return true;
        }
    }
}
