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
