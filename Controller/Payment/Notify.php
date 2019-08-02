<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Phrase;

class Notify extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Imagina\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Imagina\Payulatam\Logger\Logger
     */
    protected $logger;

     /**
     * @var \Imagina\Payulatam\Model\Order
     */
    protected $orderHelper;

    protected $orderRepository;
    protected $jsonResultFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Imagina\Payulatam\Model\ClientFactory $clientFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Imagina\Payulatam\Logger\Logger $logger
     * @param \Imagina\Payulatam\Model\Order $orderHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Imagina\Payulatam\Model\ClientFactory $clientFactory,
        \Imagina\Payulatam\Model\Order $orderHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Imagina\Payulatam\Logger\Logger $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->clientFactory = $clientFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->logger = $logger;
        $this->orderHelper = $orderHelper;
        $this->orderRepository = $orderRepository;
        $this->jsonResultFactory = $jsonResultFactory;

    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
    

    public function execute()
    {
       
        $request = $this->context->getRequest();
        try {

            $orderId = $request->getParam('extra3');
            $this->logger->info("Notify - OrderId:".$orderId);

            $orderRepo = $this->orderRepository->get($orderId);
            $this->logger->info("Notify - Current OrderState:".$orderRepo->getState());

            /**
             * If state is CANCELED = User can retry payment on PayU or do nothing
             */
            if($orderRepo->getState()=="pending_payment" || $orderRepo->getState()=="canceled"){
                $client = $this->clientFactory->create();
                $config = $client->getConfigHelper();
                $order = $this->orderHelper->loadOrderById($orderId);
                $this->checkSign($request,$config);
                $this->checkStatus($request,$order,$orderId);
            }
            
           
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
        
        //$resultForward = $this->resultForwardFactory->create();
        //$resultForward->forward('noroute');
        //return $resultForward;

        //return $this->getResponse()->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_200)->setContent('OK');

        $result = $this->jsonResultFactory->create();
        $result->setHttpResponseCode(\Magento\Framework\App\Response\Http::STATUS_CODE_200);
        $result->setData(['message' => __('OK')]);
        return $result;
        
    }

    public function checkSign($request,$config){
        
        if (!$request->isPost()) {
            throw new LocalizedException(new Phrase('POST request is required.'));
        }

        $apiKey = $config->getConfig('ApiKey');
        $merchanId = $request->getParam('merchant_id');
        $new_value = $request->getParam('value');
        $currency = $request->getParam('currency');
        $state_pol = $request->getParam('state_pol');
        $referenceSale = $request->getParam('reference_sale');
        $sigReq = $request->getParam('sign');

        $this->logger->info("Notify - ReferenceSale: ".$referenceSale);
        
        $signature = $this->signatureGeneration($apiKey,$merchanId,$referenceSale,$new_value,$currency,$state_pol);

        if (strtoupper($signature) == strtoupper($sigReq)) {
            $this->logger->info("Notify - Signature: OK");
        }else{
            throw new LocalizedException(new Phrase('Invalid Signature.'));
        }
       
    }

    public function signatureGeneration($apiKey,$merchantId,$referenceSale,$new_value,$currency,$state_pol){
        
        $split = explode('.', $new_value);
        $decimals = $split[1];

        if ($decimals % 10 == 0) {
            $value = number_format($new_value, 1, '.', '');
        }else{
            $value = $new_value;
        }

        $signature_local = $apiKey.'~'.$merchantId.'~'.$referenceSale.'~'.$value.'~'.$currency.'~'.$state_pol;

        $signature_md5 = md5($signature_local);

        return $signature_md5;
        
    }

    public function checkStatus($request,$order,$orderId){

        $transactionState = $request->getParam('state_pol');
        $polResponseCode = $request->getParam('response_code_pol');

        if($transactionState == 6 && $polResponseCode == 5){

            $statusPTP = "FALLIDA";
            $status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
            
        } else if($transactionState == 6 && $polResponseCode == 4){

            $statusPTP = "REINTEGRADO"; 
            $status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
           
        } else if($transactionState == 12 && $polResponseCode == 9994){
            
            $statusPTP = "PENDIENTE";
            $status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;

        } else if($transactionState == 4 && $polResponseCode == 1){

            $statusPTP = "APROBADA";
            $status = \Magento\Sales\Model\Order::STATE_PROCESSING;
            
        }else{

            $statusPTP = "RECHAZADA";
            $status = \Magento\Sales\Model\Order::STATE_CANCELED;
           
        }

        $order->setStatus($status);
        $order->setState($status);
        $order->addStatusHistoryComment(__('PayU Status Recibido') . ': ' . $statusPTP);
        $order->save();

        $msjLog = "Notify - OrderID: ".$orderId." - StatusPTP: ".$statusPTP;
        $this->logger->info($msjLog);

    }

}
