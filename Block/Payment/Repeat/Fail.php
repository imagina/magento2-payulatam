<?php

namespace Icyd\Payulatam\Block\Payment\Repeat;

class Fail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Icyd\Payulatam\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Icyd\Payulatam\Model\Session $session,
        \Icyd\Payulatam\Helper\Payment $paymentHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->session = $session;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return string|false
     */
    public function getPaymentUrl()
    {
        $orderId = $this->session->getLastOrderId();
        if ($orderId) {
            $repeatPaymentUrl = $this->paymentHelper->getRepeatPaymentUrl($orderId);
            if (!$repeatPaymentUrl) {
                return $this->paymentHelper->getStartPaymentUrl($orderId);
            }
            return $repeatPaymentUrl;
        }
        return false;
    }
}
