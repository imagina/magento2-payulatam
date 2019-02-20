<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout\Order;

class DataValidator extends \Icyd\Payulatam\Model\Client\DataValidator
{
    /**
     * @var array
     */
    protected $requiredBasicKeys = [
        'amount',
        'description',
        'buyerFullName',
        'buyerEmail',
        'referenceCode',
        'currency'
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
     * @return array
     */
    protected function getRequiredBasicKeys()
    {
        return $this->requiredBasicKeys;
    }
}
