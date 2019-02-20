<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout;

class MethodCaller extends \Icyd\Payulatam\Model\Client\MethodCaller
{
    public function __construct(
        MethodCaller\Raw $rawMethod,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct(
            $rawMethod,
            $logger
        );
    }
}
