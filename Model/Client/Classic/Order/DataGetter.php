<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client\Classic\Order;

class DataGetter
{
    /**
     * @var \Imagina\Payulatam\Model\Order\ExtOrderId
     */
    protected $extOrderIdHelper;

    /**
     * @var \Imagina\Payulatam\Model\Client\Classic\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Imagina\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @param \Imagina\Payulatam\Model\Order\ExtOrderId $extOrderIdHelper
     * @param \Imagina\Payulatam\Model\Client\Classic\Config $configHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Imagina\Payulatam\Model\Session $session
     */
    public function __construct(
        \Imagina\Payulatam\Model\Order\ExtOrderId $extOrderIdHelper,
        \Imagina\Payulatam\Model\Client\Classic\Config $configHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Imagina\Payulatam\Model\Session $session
    ) {
        $this->extOrderIdHelper = $extOrderIdHelper;
        $this->configHelper = $configHelper;
        $this->dateTime = $dateTime;
        $this->session = $session;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getBasicData(\Magento\Sales\Model\Order $order)
    {
        $incrementId = $order->getIncrementId();
        $billingAddress = $order->getBillingAddress();

        $taxReturnBase = number_format(($order->getGrandTotal() - $order->getTaxAmount()),2,'.','');
        if($order->getTaxAmount() == 0) $taxReturnBase = 0;

       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $urlBase = $storeManager->getStore()->getBaseUrl();

        $data = [
            'amount' => number_format($order->getGrandTotal(),2,'.',''),
            'description' => __('Order # %1', [$incrementId]) . " ",
            'extra1' => $incrementId,
            'extra2' => 'Imagina_Payulatam_M2',
            'buyerFullName' => $billingAddress->getFirstname(). ' '.$billingAddress->getLastname(),
            'buyerEmail' => $order->getCustomerEmail(),
            'referenceCode' => $this->extOrderIdHelper->generate($order),
            'currency' => $order->getOrderCurrencyCode(),
            'tax' => number_format($order->getTaxAmount(),2,'.',''),
            'taxReturnBase' => $taxReturnBase,
            'responseUrl' => $urlBase.'payulatam/payment/end',
            'confirmationUrl' => $urlBase.'payulatam/payment/notify/',
            'extra3' => $order->getId()
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->configHelper->getConfig('merchantId');
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->configHelper->getConfig('accountId');
    }

    /**
     * @return string
     */
    public function getTestMode()
    {
        return $this->configHelper->getConfig('test');
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return int
     */
    public function getTs()
    {
        return $this->dateTime->timestamp();
    }

    /**
     * @param array $data
     * @return string
     */
    public function getSigForOrderCreate(array $data = [])
    {
        //Signature Format
        //“ApiKey~merchantId~referenceCode~amount~currency”.

        return md5(
            $this->configHelper->getConfig('ApiKey')."~".
            $data['merchantId'] ."~".
            $data['referenceCode'] ."~".
            $data['amount']."~".
            $data['currency']
        );
    }

    /**
     * @param array $data
     * @return string
     */
    public function getSigForOrderRetrieve(array $data = [])
    {
        return md5(
            $data['pos_id'] .
            $data['referenceCode'] .
            $data['ts'] .
            $this->configHelper->getConfig('key_md5')
        );
    }
}
