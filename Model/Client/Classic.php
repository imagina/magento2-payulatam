<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client;

class Classic extends \Imagina\Payulatam\Model\Client
{
    /**
     * @param Classic\Config $configHelper
     * @param Classic\Order $orderHelper
     */
    public function __construct(
        Classic\Config $configHelper,
        Classic\Order $orderHelper
    ) {
        parent::__construct(
            $configHelper,
            $orderHelper
        );
    }
}
