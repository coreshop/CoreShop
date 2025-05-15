/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.shippingrule.actions.shippingRule');
coreshop.shippingrule.actions.shippingRule = Class.create(coreshop.rules.actions.abstract, {
    type: 'shippingRule',

    getForm: function () {
        var me = this;

        var rule = {
            xtype: 'combo',
            fieldLabel: t('coreshop_action_shippingRule'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_carrier_shipping_rules'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'shippingRule',
            maxHeight: 400,
            delimiter: false,
            value: me.data.shippingRule
        };

        if (this.data && this.data.shippingRule) {
            rule.value = this.data.shippingRule;
        }

        this.form = new Ext.form.Panel({
            items: [
                rule
            ]
        });

        return this.form;
    }
});
