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
