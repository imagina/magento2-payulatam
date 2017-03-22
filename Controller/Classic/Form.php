<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Controller\Classic;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Imagina\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Imagina\Payulatam\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Imagina\Payulatam\Model\Session $session,
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
            $resultPage = $this->resultPageFactory->create(true, ['template' => 'Imagina_Payulatam::emptyroot.phtml']);
            $resultPage->addHandle($resultPage->getDefaultLayoutHandle());


            $resultPage->getLayout()->getBlock('payulatam.classic.form')->setOrderCreateData($orderCreateData);
            $resultPage->getLayout()->getBlock('payulatam.classic.form')->setGatewayUrl($gatewayUrl);
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }
}
