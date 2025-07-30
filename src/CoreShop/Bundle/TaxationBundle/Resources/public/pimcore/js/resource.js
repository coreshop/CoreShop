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

pimcore.registerNS('coreshop.taxation.resource');
coreshop.taxation.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_tax_rates', 'coreshop_tax_rate_list', [
            {name: 'id'},
            {name: 'name'},
            {name: 'rate'}
        ]);
        coreshop.global.addStoreWithRoute('coreshop_taxrulegroups', 'coreshop_tax_rule_group_list');
        coreshop.global.addStoreWithRoute('coreshop_tax_rule_groups', 'coreshop_tax_rule_group_list');

        coreshop.broker.fireEvent('resource.register', 'coreshop.taxation', this);
    },

    openResource: function (item) {
        if (item === 'tax_item') {
            this.openTaxItemResource();
        } else if (item === 'tax_rule_group') {
            this.openTaxRuleGroupResource();
        }
    },

    openTaxItemResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_taxes_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_taxes_panel', new coreshop.tax.panel());
        }
    },

    openTaxRuleGroupResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_tax_rule_groups_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_tax_rule_groups_panel', new coreshop.taxrulegroup.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.taxation.resource();
});
