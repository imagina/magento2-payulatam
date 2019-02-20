<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout\MethodCaller\SoapClient;

class Order extends \Zend\Soap\Client
{
    /**
     * @var int
     */
    protected $soapVersion = SOAP_1_1;
}
