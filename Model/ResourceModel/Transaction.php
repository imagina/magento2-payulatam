<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Transaction extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $date;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct(
            $context,
            $resourcePrefix
        );
        $this->date = $date;
    }

    /**
     * @param int $orderId
     * @return string|false
     */
    public function getLastPayuplOrderIdByOrderId($orderId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $this->_resources->getTableName('sales_payment_transaction')],
                ['txn_id']
            )->where('order_id = ?', $orderId)
            ->where('txn_type = ?', 'order')
            ->order('transaction_id ' . \Zend_Db_Select::SQL_DESC)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row) {
            return $row['txn_id'];
        }
        return false;
    }

    /**
     * @param string $payuplOrderId
     * @return bool
     */
    public function checkIfNewestByPayuplOrderId($payuplOrderId)
    {
        $transactionTableName = $this->_resources->getTableName('sales_payment_transaction');
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $transactionTableName],
                ['transaction_id']
            )->joinLeft(
                ['t2' => $transactionTableName],
                't2.order_id = main_table.order_id AND t2.transaction_id > main_table.transaction_id',
                ['newer_id' => 't2.transaction_id']
            )->where('main_table.txn_id = ?', $payuplOrderId)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row && is_null($row['newer_id'])) {
            return true;
        }
        return false;
    }

    /**
     * @param string $payuplOrderId
     * @return int|false
     */
    public function getOrderIdByPayuplOrderId($payuplOrderId)
    {
        return $this->getOneFieldByAnother('order_id', 'txn_id', $payuplOrderId);
    }

    /**
     * @param string $payuplOrderId
     * @return string|false
     */
    public function getStatusByPayuplOrderId($payuplOrderId)
    {
        return $this->getAdditionalDataByPayuplOrderId($payuplOrderId, 'status');
    }

    /**
     * @param int $orderId
     * @return int
     */
    public function getLastTryByOrderId($orderId)
    {
        return $this->getLastAdditionalDataFieldByOrderId($orderId, 'try', 0);
    }

    /**
     * @param string $payuplOrderId
     * @return string|false
     */
    public function getExtOrderIdByPayuplOrderId($payuplOrderId)
    {
        return $this->getAdditionalDataByPayuplOrderId($payuplOrderId, 'order_id');
    }

    /**
     * @param string $payuplOrderId
     * @return int|false
     */
    public function getIdByPayuplOrderId($payuplOrderId)
    {
        return $this->getOneFieldByAnother('transaction_id', 'txn_id', $payuplOrderId);
    }

    /**
     * @param int $orderId
     * @return string|false
     */
    public function getLastStatusByOrderId($orderId)
    {
        return $this->getLastAdditionalDataFieldByOrderId($orderId, 'status', false);
    }

    protected function _construct()
    {

    }

    /**
     * @param string $getFieldName
     * @param string $byFieldName
     * @param mixed $value
     * @return mixed|false
     */
    protected function getOneFieldByAnother($getFieldName, $byFieldName, $value)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $this->_resources->getTableName('sales_payment_transaction')],
                [$getFieldName]
            )->where($byFieldName . ' = ?', $value)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row) {
            return $row[$getFieldName];
        }
        return false;
    }

    /**
     * @param string $payuplOrderId
     * @param string $field
     * @return mixed
     */
    protected function getAdditionalDataByPayuplOrderId($payuplOrderId, $field)
    {
        $serializedAdditionalInformation = $this->getOneFieldByAnother(
            'additional_information',
            'txn_id',
            $payuplOrderId
        );
        if ($serializedAdditionalInformation) {
            $additionalInformation = unserialize($serializedAdditionalInformation);
            return $additionalInformation[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS][$field];
        }
        return false;
    }

    /**
     * @param $orderId
     * @param $field
     * @param $valueIfNotFound
     * @return mixed
     */
    protected function getLastAdditionalDataFieldByOrderId($orderId, $field, $valueIfNotFound)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $this->_resources->getTableName('sales_payment_transaction')],
                ['additional_information']
            )->where('order_id = ?', $orderId)
            ->where('txn_type = ?', 'order')
            ->order('transaction_id ' . \Zend_Db_Select::SQL_DESC)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row) {
            $additionalInformation = unserialize($row['additional_information']);
            return $additionalInformation[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS][$field];
        }
        return $valueIfNotFound;
    }
}
