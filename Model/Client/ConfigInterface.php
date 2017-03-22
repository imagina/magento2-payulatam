<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Payulatam\Model\Client;

interface ConfigInterface
{
    public function setConfig();

    public function getConfig($key);
}
