/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.report.abstractStore');
coreshop.report.abstractStore = Class.create(coreshop.report.abstract, {
    reportType: 'abstractStoreReport',

    getFilterFields: function ($super) {
        var me = this,
            store = pimcore.globalmanager.get('coreshop_stores').valueOf(),
            filter = $super();

        filter.splice(0, 0, {
            xtype: 'combo',
            fieldLabel: null,
            listWidth: 100,
            width: 200,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: false,
            triggerAction: 'all',
            name: 'store',
            queryMode: 'remote',
            maxHeight: 400,
            delimiter: false,
            listeners: {
                afterrender: function () {
                    var first;
                    if (this.store.isLoaded()) {
                        first = this.store.getAt(0);
                        this.setValue(first);
                    } else {
                        this.store.load();
                        this.store.on('load', function (store, records, options) {
                            first = store.getAt(0);
                            this.setValue(first);
                        }.bind(this));
                    }
                },
                change: function (combo, value) {
                    this.getStoreField().setValue(value);
                    this.filter();
                }.bind(this)
            }
        });

        return filter;
    },

    getStore: function () {
        if (!this.store) {
            var me = this,
                fields = ['timestamp', 'text', 'data'];

            if (Ext.isFunction(this.getStoreFields)) {
                fields = Ext.apply(fields, this.getStoreFields());
            }

            this.store = new Ext.data.Store({
                autoDestroy: true,
                remoteSort: this.remoteSort,
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/report/get-data?report=' + this.reportType,
                    actionMethods: {
                        read: 'GET'
                    },
                    reader: {
                        type: 'json',
                        rootProperty: 'data',
                        totalProperty: 'total'
                    }
                },
                fields: fields
            });

            this.store.on('beforeload', function (store, operation) {
                store.getProxy().setExtraParams(me.getFilterParams());
            });
        }

        return this.store;
    },

    getFilterParams: function ($super) {
        var params = $super();
        params['store'] = this.getStoreField().getValue();

        return params;
    }
});

