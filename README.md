# Imagina Magento 2 PayuLatam Module

*Read this in other languages: [Español](README.es.md),[English](README.md)

PayuLatam integration for Magento 2

## Key features
- Webcheckout
- multi-store support,
- Initial integration with Magento payment flow (transactions, refunds, etc.),
- logging all APIs exceptions and errors,
- test mode

## Configuration in Magento panel

The configuration can be found in Stores > Configuration > Sales > Payment Methods > Imagina PayuLatam. It should be pretty straight-forward.

## How to Install
From the command line in magento root:
```ssh
composer require imagina/magento2-payulatam
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

More Information:  https://www.imaginacolombia.com