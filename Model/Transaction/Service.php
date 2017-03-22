<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Transaction;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Service
{
    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var \Imagina\Payulatam\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @param \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Imagina\Payulatam\Model\ResourceModel\Transaction $transactionResource
     */
    public function __construct(
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Imagina\Payulatam\Model\ResourceModel\Transaction $transactionResource
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionResource = $transactionResource;
    }

    /**
     * @param string $payuplOrderId
     * @param string $status
     * @param bool $close
     * @throws LocalizedException
     */
    public function updateStatus($payuplOrderId, $status, $close = false)
    {
        /**
         * @var $transaction \Magento\Sales\Model\Order\Payment\Transaction
         */
        $id = $this->transactionResource->getIdByPayuplOrderId($payuplOrderId);
        if (!$id) {
            throw new LocalizedException(new Phrase('Transaction ' . $payuplOrderId . ' not found.'));
        }
        $transaction = $this->transactionRepository->get($id);
        if ($close) {
            $transaction->setIsClosed(1);
        }
        $rawDetailsInfo = $transaction->getAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS
        );
        $rawDetailsInfo['status'] = $status;
        $transaction
            ->setAdditionalInformation(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $rawDetailsInfo)
            ->save();
    }
}
