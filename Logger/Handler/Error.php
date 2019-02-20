<?php
/**
 * @copyright Copyright (c) 2017 Icyd Colombia (https://www.imaginacolombia.com)
 */

namespace Icyd\Payulatam\Logger\Handler;

use Monolog\Logger;

class Error extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/payulatam/error.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::ERROR;
}
