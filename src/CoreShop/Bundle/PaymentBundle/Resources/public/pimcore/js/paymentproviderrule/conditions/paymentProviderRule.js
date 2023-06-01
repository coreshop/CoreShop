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

pimcore.registerNS('coreshop.paymentproviderrule.conditions.paymentproviderRule');

coreshop.paymentproviderrule.conditions.paymentproviderRule = Class.create(coreshop.rules.conditions.abstract, {
    type: 'paymentproviderRule',

    getForm: function () {
        var me = this;
        var store = pimcore.globalmanager.get('');

        var rule = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_paymentproviderRule'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_paymentprovider_rules'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'paymentproviderRule',
            maxHeight: 400,
            delimiter: false,
            value: me.data.paymentproviderRule
        };

        if (this.data && this.data.paymentproviderRule) {
            rule.value = this.data.paymentproviderRule;
        }

        this.form = new Ext.form.Panel({
            items: [
                rule
            ]
        });

        return this.form;
    }
});
