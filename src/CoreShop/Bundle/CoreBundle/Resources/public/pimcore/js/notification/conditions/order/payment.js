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

pimcore.registerNS('coreshop.notification.rule.conditions.payment');

coreshop.notification.rule.conditions.payment = Class.create(coreshop.rules.conditions.abstract, {
    type: 'payment',

    getForm: function () {
        var paymentProvidersStore = new Ext.data.Store({
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/payment_providers/list',
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['id', 'identifier']
        });
        paymentProvidersStore.load();

        var providers = new Ext.ux.form.MultiSelect({
            typeAhead: true,
            listWidth: 100,
            width: 500,
            forceSelection: true,
            maxHeight: 400,
            delimiter: false,
            labelWidth: 150,
            fieldLabel: t('coreshop_paymentProvider'),
            mode: 'local',
            store: paymentProvidersStore,
            displayField: 'identifier',
            valueField: 'id',
            triggerAction: 'all',
            name: 'providers',
            multiSelect: true,
            value: this.data ? this.data.providers : []
        });

        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                providers
            ]
        });

        return this.form;
    }
});
