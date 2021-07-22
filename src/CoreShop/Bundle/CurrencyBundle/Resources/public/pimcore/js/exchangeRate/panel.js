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

pimcore.registerNS('coreshop.exchange_rate.panel');
coreshop.exchange_rate.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_exchange_rates_panel',
    storeId: 'coreshop_exchange_rates',
    iconCls: 'coreshop_icon_exchange_rate',
    type: 'coreshop_exchange_rate',

    url: {
        add: '/admin/coreshop/exchange_rates/add',
        save: '/admin/coreshop/exchange_rates/save',
        delete: '/admin/coreshop/exchange_rates/delete',
        get: '/admin/coreshop/exchange_rates/get',
        list: '/admin/coreshop/exchange_rates/list'
    },

    getItems: function () {
        return [this.getExchangeRatesGrid()];
    },

    getExchangeRatesGrid: function () {
        pimcore.globalmanager.get(this.storeId).load();

        var currencyStore = Ext.create('store.coreshop_currencies');
        currencyStore.load();

        this.grid = Ext.create('Ext.grid.Panel', {
            store: pimcore.globalmanager.get(this.storeId),
            region: 'center',
            columns: [
                {
                    header: t('coreshop_from_currency'),
                    flex: 1,
                    dataIndex: 'fromCurrency',
                    editor: new Ext.form.ComboBox({
                        store: currencyStore,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        required: true
                    }),
                    renderer: function (currencyId) {;
                        var currency = currencyStore.getById(currencyId);
                        if (currency) {
                            return currency.get('name');
                        }

                        return null;
                    }
                },
                {
                    header: t('coreshop_to_currency'),
                    flex: 1,
                    dataIndex: 'toCurrency',
                    editor: new Ext.form.ComboBox({
                        store: currencyStore,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        required: true
                    }),
                    renderer: function (currencyId) {
                        var currency = currencyStore.getById(currencyId);
                        if (currency) {
                            return currency.get('name');
                        }

                        return null;
                    }
                },
                {
                    header: t('coreshop_exchange_rate'),
                    width: 200,
                    dataIndex: 'exchangeRate',
                    editor: {
                        xtype: 'numberfield',
                        decimalPrecision: 10,
                        required: true
                    }
                },
                {
                    xtype: 'actioncolumn',
                    width: 40,
                    items: [{
                        iconCls: 'pimcore_icon_delete',
                        tooltip: t('delete'),
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);

                            grid.getStore().remove(rec);

                            if (!rec.phantom) {
                                Ext.Ajax.request({
                                    url: this.url.delete,
                                    jsonData: rec.data,
                                    method: 'delete',
                                    success: function (response) {

                                    }.bind(this)
                                });
                            }
                        }.bind(this)
                    }]
                }
            ],
            selModel: 'rowmodel',
            tbar: [
                {
                    text: t('add'),
                    handler: function () {
                        pimcore.globalmanager.get(this.storeId).add({});
                    }.bind(this),
                    iconCls: 'pimcore_icon_add'
                }
            ],

            plugins: Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToEdit: 1,
                listeners: {}
            })
        });

        this.grid.on('edit', function (editor, e) {
            Ext.Ajax.request({
                url: this.url.save,
                jsonData: e.record.data,
                method: 'post',
                success: function (response) {
                    var res = Ext.decode(response.responseText);

                    if (res.success) {
                        e.record.set(res.data);
                        e.record.commit();
                    } else {
                        e.record.erase();
                        pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'),
                            'error', res.message);
                    }
                }.bind(this)
            });
        }.bind(this));

        return this.grid;
    }
});
