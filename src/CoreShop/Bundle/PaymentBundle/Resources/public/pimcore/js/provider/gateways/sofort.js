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

pimcore.registerNS('coreshop.provider.gateways.sofort');
coreshop.provider.gateways.sofort = Class.create(coreshop.provider.gateways.abstract, {

    getLayout: function (config) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_payment_sofort_config_key'),
                name: 'gatewayConfig.config.config_key',
                length: 255,
                value: config.config_key ? config.config_key : ""
            }
        ];
    }

});
