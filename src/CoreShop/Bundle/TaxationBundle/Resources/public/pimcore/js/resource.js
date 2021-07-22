/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.taxation.resource');
coreshop.taxation.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_tax_rates', 'coreshop/tax_rates', [
            {name: 'id'},
            {name: 'name'},
            {name: 'rate'}
        ]);
        coreshop.global.addStore('coreshop_taxrulegroups', 'coreshop/tax_rule_groups');
        coreshop.global.addStore('coreshop_tax_rule_groups', 'coreshop/tax_rule_groups');

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
