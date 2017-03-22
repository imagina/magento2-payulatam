<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client\Classic;

class MethodCaller extends \Imagina\Payulatam\Model\Client\MethodCaller
{
    public function __construct(
        MethodCaller\Raw $rawMethod,
        \Imagina\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct(
            $rawMethod,
            $logger
        );
    }
}
