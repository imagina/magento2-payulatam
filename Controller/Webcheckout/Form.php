<?php

namespace Icyd\Payulatam\Controller\Webcheckout;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Icyd\Payulatam\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Icyd\Payulatam\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /**
         * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
         * @var $resultPage \Magento\Framework\View\Result\Page
         */
        $orderCreateData = $this->session->getOrderCreateData();
        $gatewayUrl = $this->session->getGatewayUrl();

        if ($orderCreateData) {
            //Todo: Reactivate after testing
            //$this->session->setOrderCreateData(null);
            $resultPage = $this->resultPageFactory->create(true, ['template' => 'Icyd_Payulatam::emptyroot.phtml']);
            $resultPage->addHandle($resultPage->getDefaultLayoutHandle());


            $resultPage->getLayout()->getBlock('payulatam.webcheckout.form')->setOrderCreateData($orderCreateData);
            $resultPage->getLayout()->getBlock('payulatam.webcheckout.form')->setGatewayUrl($gatewayUrl);
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }
}
