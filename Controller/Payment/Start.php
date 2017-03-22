<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Imagina\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Imagina\Payulatam\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Imagina\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Imagina\Payulatam\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Imagina\Payulatam\Model\ClientFactory $clientFactory
     * @param \Imagina\Payulatam\Model\Order $orderHelper
     * @param \Imagina\Payulatam\Model\Session $session
     * @param \Imagina\Payulatam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Imagina\Payulatam\Model\ClientFactory $clientFactory,
        \Imagina\Payulatam\Model\Order $orderHelper,
        \Imagina\Payulatam\Model\Session $session,
        \Imagina\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $clientOrderHelper \Imagina\Payulatam\Model\Client\OrderInterface
         * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = 'checkout/cart';
        $redirectParams = [];
        $orderId = $this->orderHelper->getOrderIdForPaymentStart();
        if ($orderId) {
            $order = $this->orderHelper->loadOrderById($orderId);
            if ($this->orderHelper->canStartFirstPayment($order)) {
                try {
                    $client = $this->clientFactory->create();

                    $clientOrderHelper = $client->getOrderHelper();
                    $orderData = $clientOrderHelper->getDataForOrderCreate($order);

                    $result = $client->orderCreate($orderData);

                    $this->orderHelper->addNewOrderTransaction(
                        $order,
                        $result['orderId'],
                        $result['extOrderId'],
                        $clientOrderHelper->getNewStatus()
                    );
                    $this->orderHelper->setNewOrderStatus($order);

                    $configHelper = $client->getConfigHelper();

                    $this->session->setGatewayUrl($configHelper->getConfig('url'));

                    $redirectUrl = $result['redirectUri'];
                } catch (LocalizedException $e) {
                    $this->logger->critical($e);
                    $redirectUrl = 'payulatam/payment/end';
                    $redirectParams = ['exception' => '1'];
                }
                $this->session->setLastOrderId($orderId);
            }
        }
        $resultRedirect->setPath($redirectUrl, $redirectParams);
        return $resultRedirect;
    }
}
