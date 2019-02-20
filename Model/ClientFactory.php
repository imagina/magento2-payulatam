<?php

namespace Icyd\Payulatam\Model;

class ClientFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data = [])
    {
        //Here we could select what kind of integration. Right now we only have "webcheckout". http://developers.payulatam.com/en/web_checkout/integration.html
        $class = Client\Webcheckout::class;

        return $this->objectManager->create($class, []);
    }
}
