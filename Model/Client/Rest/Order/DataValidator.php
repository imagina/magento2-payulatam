<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client\Rest\Order;

class DataValidator extends \Imagina\Payulatam\Model\Client\DataValidator
{
    /**
     * @var array
     */
    protected $requiredProductKeys = [
        'name',
        'unitPrice',
        'quantity'
    ];

    /**
     * @var array
     */
    protected $requiredBasicKeys = [
        'description',
        'currencyCode',
        'totalAmount',
        'extOrderId',
        'products'
    ];

    /**
     * @var array
     */
    protected $requiredStatusUpdateKeys = [
        'orderId',
        'orderStatus'
    ];

    /**
     * @var array
     */
    protected $validStatusUpdateOrderStatuses = [
        'COMPLETED',
        'REJECTED'
    ];

    /**
     * @param array $data
     * @return bool
     */
    public function validateBasicData(array $data = [])
    {
        foreach ($this->getRequiredBasicKeys() as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validateProductsData(array $data = [])
    {
        if (isset($data['products']) && !empty($data['products'])) {
            $requiredProductKeys = $this->getRequiredProductKeys();
            foreach ($data['products'] as $productData) {
                foreach ($requiredProductKeys as $key) {
                    if (!isset($productData[$key]) || $productData[$key] === '') {
                        return false;
                    }
                    if ($key === 'quantity' && !$this->validatePositiveFloat($productData[$key])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validateStatusUpdateData($data)
    {
        foreach ($this->getRequiredStatusUpdateKeys() as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return false;
            }
        }
        $validStatuses = $this->getValidStatusUpdateOrderStatuses();
        if (!in_array($data['orderStatus'], $validStatuses)) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    protected function getRequiredBasicKeys()
    {
        return $this->requiredBasicKeys;
    }

    /**
     * @return array
     */
    protected function getRequiredProductKeys()
    {
        return $this->requiredProductKeys;
    }

    /**
     * @return array
     */
    protected function getRequiredStatusUpdateKeys()
    {
        return $this->requiredStatusUpdateKeys;
    }

    /**
     * @return array
     */
    protected function getValidStatusUpdateOrderStatuses()
    {
        return $this->validStatusUpdateOrderStatuses;
    }
}
