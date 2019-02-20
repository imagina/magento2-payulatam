<?php

namespace Icyd\Payulatam\Block\Payment;

class Info extends \Magento\Payment\Block\Info
{
    /**
     * @var \Icyd\Payulatam\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var \Icyd\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Icyd\Payulatam\Model\ResourceModel\Transaction $transactionResource
     * @param \Icyd\Payulatam\Model\ClientFactory $clientFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Icyd\Payulatam\Model\ResourceModel\Transaction $transactionResource,
        \Icyd\Payulatam\Model\ClientFactory $clientFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->transactionResource = $transactionResource;
        $this->clientFactory = $clientFactory;
    }

    protected function _prepareLayout()
    {
        $this->addChild('buttons', Info\Buttons::class);
        parent::_prepareLayout();
    }

    protected function _prepareSpecificInformation($transport = null)
    {
        /**
         * @var $client \Icyd\Payulatam\Model\Client
         */
        $transport = parent::_prepareSpecificInformation($transport);
        $orderId = $this->getInfo()->getParentId();
        $status = $this->transactionResource->getLastStatusByOrderId($orderId);
        $client = $this->clientFactory->create();
        $statusDescription = $client->getOrderHelper()->getStatusDescription($status);
        $transport->setData((string) __('Status'), $statusDescription);
        return $transport;
    }
}
