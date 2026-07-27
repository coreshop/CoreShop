/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.provider.gateways.paypal_express_checkout');
coreshop.provider.gateways.paypal_express_checkout = Class.create(coreshop.provider.gateways.abstract, {

    getLayout: function (config) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_username'),
                name: 'gatewayConfig.config.username',
                length: 255,
                value: config.username ? config.username : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_password'),
                name: 'gatewayConfig.config.password',
                length: 255,
                value: config.password
            },
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_signature'),
                name: 'gatewayConfig.config.signature',
                length: 255,
                value: config.signature
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('coreshop_paypal_sandbox'),
                name: 'gatewayConfig.config.sandbox',
                value: config.sandbox
            }
        ];
    }

});
