<?php

namespace Icyd\Payulatam\Model\Client;

interface ConfigInterface
{
    public function setConfig();

    public function getConfig($key);
}
