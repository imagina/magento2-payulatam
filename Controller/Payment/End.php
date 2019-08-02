<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Controller\Payment;

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
     * @var \Imagina\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Imagina\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Imagina\Payulatam\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Imagina\Payulatam\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session\SuccessValidator $successValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Imagina\Payulatam\Model\Session $session
     * @param \Imagina\Payulatam\Model\ClientFactory $clientFactory
     * @param \Imagina\Payulatam\Model\Order $orderHelper
     * @param \Imagina\Payulatam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session\SuccessValidator $successValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Imagina\Payulatam\Model\Session $session,
        \Imagina\Payulatam\Model\ClientFactory $clientFactory,
        \Imagina\Payulatam\Model\Order $orderHelper,
        \Imagina\Payulatam\Logger\Logger $logger
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
         * @var $clientOrderHelper \Imagina\Payulatam\Model\Client\OrderInterface
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = '/';
        try {
            if ($this->successValidator->isValid()) {
                $redirectUrl = 'payulatam/payment/error';
                $this->session->setLastOrderId(null);
                $clientOrderHelper = $this->getClientOrderHelper();
                if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
                    
                    $request = $this->context->getRequest();
                    $redirectUrl = $this->addMsjStatus($request);

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
     * @return \Imagina\Payulatam\Model\Client\OrderInterface
     */
    protected function getClientOrderHelper()
    {
        return $this->clientFactory->create()->getOrderHelper();
    }

    /**
     * @return 
     */
    public function addMsjStatus($request){

        $transactionState = $request->getParam('transactionState');

        if ($transactionState == 4 ) {
            $msj = "Estado de la transaccion: Aprobada";
            $redirectUrl = 'checkout/onepage/success';
        }

        else if ($transactionState == 6 ) {
            $msj = "Estado de la transaccion: Rechazada";
            $this->messageManager->addError(__($msj)); 
            $redirectUrl = 'checkout/onepage/failure';
        }

        else if ($transactionState == 104 ) {
            $msj = "Estado de la transaccion: Error";
            $this->messageManager->addError(__($msj));
            $redirectUrl = 'checkout/onepage/failure';
        }

        else if ($transactionState == 7 ) {
            $msj = "Estado de la transaccion: Pendiente";
            $this->messageManager->addWarning(__($msj));
            $redirectUrl = 'checkout/onepage/failure';
        }

        else {
            $msj = $request->getParam('mensaje');
            $this->messageManager->addWarning(__($msj));
            $redirectUrl = 'checkout/onepage/failure';
        }

        return $redirectUrl;

    }

}