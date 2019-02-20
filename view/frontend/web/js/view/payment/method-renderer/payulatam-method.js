/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/place-order',
        'mage/url'
    ],
    function ($,Component, customer, placeOrderAction, urlBuilder) {
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'Icyd_Payulatam/payment/payulatam'
            },

            getData: function() {
                return {
                    "method": this.item.method,
                    "additional_data": this.getAdditionalData()
                };
            },
            getAdditionalData: function() {
                return null;
            },

            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder);
                    $.when(placeOrder).done(function () {
                        $.mage.redirect(urlBuilder.build('/payulatam/payment/start'));
                    }).fail(function(){
                        self.isPlaceOrderActionAllowed(true);
                    });
                    return true;
                }
                return false;
            }

           
        });
    }
);
