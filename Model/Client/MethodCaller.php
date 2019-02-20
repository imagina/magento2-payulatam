<?php

namespace Icyd\Payulatam\Model\Client;

use Icyd\Payulatam\Model\Client\MethodCallerInterface;

class MethodCaller implements MethodCallerInterface
{
    /**
     * @var MethodCaller\RawInterface
     */
    protected $_rawMethod;

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $_logger;

    /**
     * @param MethodCaller\RawInterface $rawMethod
     * @param \Icyd\Payulatam\Logger\Logger $logger
     */
    public function __construct(
        MethodCaller\RawInterface $rawMethod,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        $this->_rawMethod = $rawMethod;
        $this->_logger = $logger;
    }

    /**
     * @param string $methodName
     * @param array $args
     * @return \stdClass|false
     */
    public function call($methodName, array $args = [])
    {
        try {
            return $this->_rawMethod->call($methodName, $args);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return false;
        }
    }
}
