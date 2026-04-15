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

pimcore.registerNS('coreshop.notification.rule.conditions.stores');

coreshop.notification.rule.conditions.stores = Class.create(coreshop.rules.conditions.abstract, {
    type: 'stores',

    getForm: function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_stores');

        var stores = {
            fieldLabel: t('coreshop_store'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiSelect: true,
            triggerAction: 'all',
            name: 'stores',
            maxHeight: 400,
            delimiter: false,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.stores)
                        this.setValue(me.data.stores);
                }
            }
        };

        if (this.data && this.data.stores) {
            stores.value = this.data.stores;
        }

        stores = new Ext.ux.form.MultiSelect(stores);

        this.form = new Ext.form.Panel({
            items: [
                stores
            ]
        });

        return this.form;
    }
});
