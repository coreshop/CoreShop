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

pimcore.registerNS('pimcore.plugin.coreshop.orders.grid');
pimcore.plugin.coreshop.orders.grid = Class.create({

    layoutId: 'coreshop_orders',

    grid: null,

    store: null,

    columns: [],
    storeFields: [],

    initialize: function () {
        this.panels = [];

        Ext.Ajax.request({
            url: '/admin/coreshop/order/get-order-grid-configuration',
            method: 'GET',
            success: function (result) {
                var columnConfig = Ext.decode(result.responseText);

                this.prepareConfig(columnConfig.columns);

                this.getLayout();
            }.bind(this)
        });
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },


    prepareConfig: function (columnConfig) {
        var gridColumns = [];
        var storeModelFields = [];

        Ext.each(columnConfig, function (column) {
            var newColumn = column;
            var storeModelField = {
                name: column.dataIndex,
                type: column.type
            };

            newColumn.id = newColumn.dataIndex;
            newColumn.text = newColumn.text.split('|').map(function (string) {
                //text like [foo bar] won't be translated. just remove brackets.
                return string.match(/\[([^)]+)]/) ? string.replace(/\[|]/gi, '') : t(string);
            }).join(' ');

            if (newColumn.hasOwnProperty('renderAs')) {
                if (newColumn.renderAs === 'currency') {
                    newColumn.renderer = this.currencyRenderer;
                }
                else if (newColumn.renderAs === 'orderState') {
                    newColumn.renderer = this.orderStateRenderer;
                }
                else if (newColumn.renderAs === 'shop') {
                    newColumn.renderer = this.shopRenderer;
                }
            }

            if (newColumn.type === 'date') {
                newColumn.xtype = 'datecolumn';
                newColumn.format = t('coreshop_date_time_format');

                storeModelField.dateFormat = 'timestamp';
            }

            if (newColumn.type === 'integer' || newColumn.type === 'float') {
                newColumn.xtype = 'numbercolumn';
            }

            storeModelFields.push(storeModelField);
            gridColumns.push(newColumn);
        }.bind(this));

        this.columns = gridColumns;
        this.storeFields = storeModelFields;
    },

    currencyRenderer: function (value, metaData, record) {
        var currency = record.get('currency').symbol;

        return coreshop.util.format.currency(currency, value);
    },

    orderStateRenderer: function (orderStateInfo) {
        if (orderStateInfo.state) {
            var bgColor = orderStateInfo.state.color,
                textColor = coreshop.helpers.constrastColor(bgColor);

            return '<span class="rounded-color" style="background-color:' + bgColor + '; color: ' + textColor + '">' + orderStateInfo.state.translatedLabel + '</span>';
        }

        return null;
    },

    shopRenderer: function (val) {
        var store = pimcore.globalmanager.get('coreshop_stores');
        var pos = store.findExact('id', String(val));
        if (pos >= 0) {
            var shop = store.getAt(pos);

            return shop.get('name');
        }

        return null;
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: this.layoutId,
                title: t('coreshop_orders'),
                iconCls: 'coreshop_icon_orders',
                border: false,
                layout: 'border',
                closable: true,
                items: this.getItems(),
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            iconCls: 'coreshop_icon_order_create',
                            text: t('coreshop_order_create'),
                            handler: function () {
                                coreshop.helpers.createOrder();
                            }.bind(this)
                        }
                    ]
                }]
            });

            // add event listener
            var layoutId = this.layoutId;
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove(layoutId);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getItems: function () {
        return [this.getGrid()];
    },

    getGrid: function () {

        this.store = new Ext.data.JsonStore({
            remoteSort: true,
            remoteFilter: true,
            autoDestroy: true,
            autoSync: true,
            pageSize: pimcore.helpers.grid.getDefaultPageSize(),
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/order/get-orders',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    totalProperty: 'total'
                }
            },

            //alternatively, a Ext.data.Model name can be given (see Ext.data.Store for an example)
            fields: this.storeFields
        });

        this.grid = Ext.create('Ext.grid.Panel', {
            title: t('coreshop_orders'),
            store: this.store,
            plugins: 'gridfilters',
            columns: this.columns,
            region: 'center',
            stateful: true,
            stateId: 'coreshop_order_grid',
            // paging bar on the bottom
            bbar: this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store),
            listeners: {
                itemclick: function (grid, record) {
                    grid.setLoading(t('loading'));

                    this.openOrder(record, function () {
                        grid.setLoading(false);
                    }.bind(this));
                }.bind(this)
            }
        });

        return this.grid;
    },

    openOrder: function (record, callback) {
        coreshop.helpers.openOrder(record.get('o_id'), callback);
    }
});
