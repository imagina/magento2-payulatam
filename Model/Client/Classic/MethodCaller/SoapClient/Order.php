<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client\Classic\MethodCaller\SoapClient;

class Order extends \Zend\Soap\Client
{
    /**
     * @var int
     */
    protected $soapVersion = SOAP_1_1;
}
