<?php

namespace Icyd\Payulatam\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class End extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    protected $successValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Icyd\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Icyd\Payulatam\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session\SuccessValidator $successValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Icyd\Payulatam\Model\Session $session
     * @param \Icyd\Payulatam\Model\ClientFactory $clientFactory
     * @param \Icyd\Payulatam\Model\Order $orderHelper
     * @param \Icyd\Payulatam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session\SuccessValidator $successValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Icyd\Payulatam\Model\Session $session,
        \Icyd\Payulatam\Model\ClientFactory $clientFactory,
        \Icyd\Payulatam\Model\Order $orderHelper,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->successValidator = $successValidator;
        $this->checkoutSession = $checkoutSession;
        $this->session = $session;
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $clientOrderHelper \Icyd\Payulatam\Model\Client\OrderInterface
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = '/';
        try {
            if ($this->successValidator->isValid()) {
                // $redirectUrl = 'payulatam/payment/error';
                $redirectUrl = 'checkout/onepage/failure';
                // $this->session->setLastOrderId(null);
                $clientOrderHelper = $this->getClientOrderHelper();
                if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
                    $redirectUrl = 'checkout/onepage/success';
                }

            } else {
                if ($this->session->getLastOrderId()) {
                    $redirectUrl = 'payulatam/payment/repeat_error';
                    $clientOrderHelper = $this->getClientOrderHelper();
                    if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
                        $redirectUrl = 'payulatam/payment/repeat_success';
                    }
                }
            }
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
        $resultRedirect->setPath($redirectUrl);
        return $resultRedirect;
    }

    /**
     * @return \Icyd\Payulatam\Model\Client\OrderInterface
     */
    protected function getClientOrderHelper()
    {
        return $this->clientFactory->create()->getOrderHelper();
    }
}
