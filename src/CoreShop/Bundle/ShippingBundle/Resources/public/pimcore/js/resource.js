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

pimcore.registerNS('coreshop.shipping.resource');
coreshop.shipping.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_carriers', 'coreshop_carrier_list', [
            [
                {name: 'id'},
                {name: 'identifier'}
            ]
        ]);
        coreshop.global.addStoreWithRoute('coreshop_carrier_shipping_rules', 'coreshop_shipping_rule_list');

        coreshop.broker.fireEvent('resource.register', 'coreshop.shipping', this);
    },

    openResource: function(item) {
        if (item === 'carrier') {
            this.openCarrierResource();
        } else if (item === 'shipping_rules') {
            this.openShippingRules();
        }
    },

    openCarrierResource: function() {
        try {
            pimcore.globalmanager.get('coreshop_carriers_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_carriers_panel', new coreshop.carrier.panel());
        }
    },

    openShippingRules: function() {
        try {
            pimcore.globalmanager.get('coreshop_carrier_shipping_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_carrier_shipping_rule_panel', new coreshop.shippingrule.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.shipping.resource();
});
