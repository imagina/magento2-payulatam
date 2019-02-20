# Modulo de Pago de Magento 2 para PayuLatam

Modulo de integración de PayuLatam para Magento 2

## Características
- Webcheckout.
- Soporte a multi-tienda,
- Integración Inicial con el flujo de pago de Magento 2 (transaccioness, reintentos, etc.),
- Registro de acciones, errores, etc
- Modo de Prueba, con configuración automática según país.

## Panel de configuración

Lo puede encontrar en "Stores > Configuration > Sales > Payment Methods > Icyd PayuLatam."

## Como instalar
Desde la línea de comandos en la raíz de magento:
```ssh
git clone https://github.com/icyd/magento2-payulatam app/code/Icyd/Payulatam
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
php bin/magento cache:clean
```

## Atribuciones
Modulo adaptado del trabajo de [ORBA/magento2-payulatam](https://github.com/ORBA/magento2_payupl) y [Imagina/magento2-payulatam](https://github.com/imagina/magento2-payulatam), bajo licencias [OLS 3.0](https://opensource.org/licenses/OSL-3.0) [AFL 3.0](https://opensource.org/licenses/AFL-3.0) y [CC-BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0/s). Copyright © 2018 Alberto Vázquez.
(

