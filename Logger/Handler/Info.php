<?php

namespace Imagina\Payulatam\Logger\Handler;

use Monolog\Logger;

class Info extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/imagina/payulatam/info.log';
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
}