/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
