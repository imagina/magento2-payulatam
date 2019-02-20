<?php
/**
 * @copyright Copyright (c) 2017 Icyd Colombia (https://www.imaginacolombia.com)
 */

namespace Icyd\Payulatam\Logger\Handler;

use Monolog\Logger;

class Exception extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/payulatam/exception.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::CRITICAL;
}
