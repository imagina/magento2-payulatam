<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Icyd\Payulatam\Model\Client\ConfigInterface;
use Icyd\Payulatam\Model\Payulatam;

class Config implements ConfigInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $accountId;

    /**
     * @var string
     */
    protected $ApiKey;

    /**
     * @var string
     */
    protected $ApiLogin;

    /**
     * @var string
     */
    protected $test;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $country;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return true
     * @throws LocalizedException
     */
    public function setConfig()
    {


        $merchantId = $this->scopeConfig->getValue(Payulatam::XML_PATH_MERCHANT_ID, 'store');
        if ($merchantId) {
            $this->merchantId = $merchantId;
        } else {
            throw new LocalizedException(new Phrase('merchantId is empty.'));
        }

        $accountId = $this->scopeConfig->getValue(Payulatam::XML_PATH_ACCOUNT_ID, 'store');
        if ($accountId) {
            $this->accountId = $accountId;
        } else {
            throw new LocalizedException(new Phrase('accountId is empty.'));
        }

        $ApiKey = $this->scopeConfig->getValue(Payulatam::XML_PATH_API_KEY, 'store');
        if ($ApiKey) {
            $this->ApiKey = $ApiKey;
        } else {
            throw new LocalizedException(new Phrase('ApiKey is empty.'));
        }

        $ApiLogin = $this->scopeConfig->getValue(Payulatam::XML_PATH_API_LOGIN, 'store');
        if ($ApiLogin) {
            $this->ApiLogin = $ApiLogin;
        } else {
            throw new LocalizedException(new Phrase('ApiLogin is empty.'));
        }


        if ($this->scopeConfig->isSetFlag(Payulatam::XML_PATH_TEST, 'store')) {
            $this->test = 1;
            $this->url = 'https://sandbox.gateway.payulatam.com/ppp-web-gateway/';
        } else {
            $this->test = 0;
            $this->url = 'https://gateway.payulatam.com/ppp-web-gateway/';
        }

        $country = $this->scopeConfig->getValue(Payulatam::XML_PATH_COUNTRY, 'store');
        if ($country) {
            $this->country = $country;
        } else {
            throw new LocalizedException(new Phrase('country is empty.'));
        }


        return true;
    }

    /**
     * @param string|null $key
     * @return string|array
     */
    public function getConfig($key = null)
    {
        $config = [
            'merchantId' => $this->merchantId,
            'accountId' => $this->accountId,
            'ApiKey' => $this->ApiKey,
            'ApiLogin' => $this->ApiLogin,
            'test' => $this->test,
            'url' => $this->url,
            'country' => $this->country
        ];
        if ($key) {
            return $config[$key];
        }
        return $config;
    }
}
