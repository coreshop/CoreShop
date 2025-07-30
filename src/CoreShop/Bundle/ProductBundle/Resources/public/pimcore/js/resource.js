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

pimcore.registerNS('coreshop.product.resource');
coreshop.product.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_product_units', 'coreshop_product_unit_list');
        pimcore.globalmanager.get('coreshop_product_units').load();
        coreshop.broker.fireEvent('resource.register', 'coreshop.product', this);
    },

    openResource: function (item) {
        if (item === 'product_price_rule') {
            this.openProductPriceRule();
        }if (item === 'product_unit') {
            this.openProductUnit();
        }
    },

    openProductPriceRule: function () {
        try {
            pimcore.globalmanager.get('coreshop_product_price_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_product_price_rule_panel', new coreshop.product.pricerule.panel());
        }
    },

    openProductUnit: function () {
        try {
            pimcore.globalmanager.get('coreshop_product_unit_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_product_unit_panel', new coreshop.product.unit.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.product.resource();
});
