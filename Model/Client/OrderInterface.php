<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client;

use Magento\Framework\Exception\LocalizedException;

interface OrderInterface
{
    /**
     * @param array $data
     * @return bool
     */
    public function validateCreate(array $data = []);

    /**
     * @param $id
     * @return bool
     */
    public function validateRetrieve($id);

    /**
     * @param $id
     * @return bool
     */
    public function validateCancel($id);

    /**
     * @param array $data
     * @return bool
     */
    public function validateStatusUpdate(array $data = []);

    /**
     * Returns false on fail or array with the following keys on success: orderId, redirectUri, extOrderId
     *
     * @param array $data
     * @return array|false
     */
    public function create(array $data);

    /**
     * Return false on fail or array with the following keys: status, amount on success.
     *
     * @param string $payulatamOrderId
     * @return array|false
     */
    public function retrieve($payulatamOrderId);

    /**
     * Return false on fail or true success.
     *
     * @param string $payulatamOrderId
     * @return bool
     */
    public function cancel($payulatamOrderId);

    /**
     * Return false on fail or true success.
     *
     * @param array $data
     * @return bool
     */
    public function statusUpdate(array $data = []);

    /**
     * Returns false on fail or array with the following keys on success: payulatamOrderId, status, amount
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @return array|false
     */
    public function consumeNotification(\Magento\Framework\App\Request\Http $request);

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getDataForOrderCreate(\Magento\Sales\Model\Order $order);

    /**
     * Adds API-related special data to standard order data.
     *
     * @param array $data
     * @return array
     */
    public function addSpecialDataToOrder(array $data = []);

    /**
     * @return string
     */
    public function getNewStatus();

    /**
     * Checks if payment was successful.
     *
     * @return bool
     */
    public function paymentSuccessCheck();

    /**
     * @param string $payulatamOrderId
     * @return bool
     */
    public function canProcessNotification($payulatamOrderId);

    /**
     * @param string $payulatamOrderId
     * @param string $status
     * @param float $amount
     * @return \Magento\Framework\Controller\Result\Raw
     * @throws LocalizedException
     */
    public function processNotification($payulatamOrderId, $status, $amount);

    /**
     * @param mixed $status
     * @return string
     */
    public function getStatusDescription($status);
}
