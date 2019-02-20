<?php

namespace Icyd\Payulatam\Model\Client;

class Webcheckout extends \Icyd\Payulatam\Model\Client
{
    /**
     * @param Webcheckout\Config $configHelper
     * @param Webcheckout\Order $orderHelper
     */
    public function __construct(
        Webcheckout\Config $configHelper,
        Webcheckout\Order $orderHelper
    ) {
        parent::__construct(
            $configHelper,
            $orderHelper
        );
    }
}
