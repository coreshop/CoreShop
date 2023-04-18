/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.conditions.not_combinable');
coreshop.cart.pricerules.conditions.not_combinable = Class.create(coreshop.rules.conditions.abstract, {

    type: 'not_combinable',

    getForm: function () {
        var me = this;

        var price_rules = {
            fieldLabel: t('coreshop_condition_not_combinable'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_cart_price_rules'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'price_rules',
            height: 400,
            delimiter: false,
            value: me.data.countries
        };


        if (this.data && this.data.price_rules) {
            price_rules.value = this.data.price_rules;
        }

        price_rules = new Ext.ux.form.MultiSelect(price_rules);

        this.form = new Ext.form.Panel({
            items: [
                price_rules
            ]
        });

        return this.form;
    }
});
