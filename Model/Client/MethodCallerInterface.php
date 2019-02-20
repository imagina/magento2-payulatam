<?php

namespace Icyd\Payulatam\Model\Client;

interface MethodCallerInterface
{
    /**
     * @param string $methodName
     * @param array $args
     * @return mixed
     */
    public function call($methodName, array $args = []);
}
