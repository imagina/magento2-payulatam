<?php
namespace Imagina\Payulatam\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class Payulatam  extends AbstractMethod
{
    const CODE = 'imagina_payulatam';

    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_stripeApi = false;

    /**
     * @var string
    */
    protected $_code = self::CODE;

    protected $_supportedCurrencyCodes = array('ARS','BRL','CLP','COP','MXN','PEN','USD');

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;


    public function __construct(\Magento\Framework\Model\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
                                \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
                                \Magento\Payment\Helper\Data $paymentData,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Payment\Model\Method\Logger $logger,
                                \Magento\Framework\Module\ModuleListInterface $moduleList,
                                \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
                                \Magento\Directory\Model\CountryFactory $countryFactory,
                                \Magento\Framework\UrlInterface $urlBuilder,
                                array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $extensionFactory, $customAttributeFactory,
            $paymentData, $scopeConfig, $logger, $moduleList, $localeDate, null,
            $urlBuilder, $data
        );


        $this->urlBuilder = $urlBuilder;

    }


    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->urlBuilder->getUrl('orba_payupl/payment/start');
    }
}