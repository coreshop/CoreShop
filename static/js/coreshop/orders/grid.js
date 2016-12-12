/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.orders.grid');
pimcore.plugin.coreshop.orders.grid = Class.create({

    layoutId : 'coreshop_orders',

    initialize: function () {
        // create layout
        this.getLayout();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
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
                            iconCls : 'coreshop_icon_order_create',
                            text : t('coreshop_order_create'),
                            handler : function () {
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

    getGrid : function () {
        this.store = new Ext.data.JsonStore({
            remoteSort: true,
            remoteFilter: true,
            autoDestroy: true,
            autoSync: true,
            pageSize: pimcore.helpers.grid.getDefaultPageSize(),
            proxy: {
                type: 'ajax',
                url: '/plugin/CoreShop/admin_order/get-orders',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    totalProperty : 'total'
                }
            },

            //alternatively, a Ext.data.Model name can be given (see Ext.data.Store for an example)
            fields: [
                'o_id',
                'orderState',
                { name:'orderDate', type: 'date', dateFormat: 'timestamp' },
                'orderNumber',
                'lang',
                'carrier',
                { name : 'discount', type : 'float' },
                { name : 'subtotal', type : 'float' },
                { name : 'shipping', type : 'float' },
                { name : 'paymentFee', type : 'float' },
                { name : 'totalTax', type : 'float' },
                { name : 'total', type : 'float' },
                { name : 'shop', type : 'integer' },
            ]
        });

        var columns = [
            {
                text: t('coreshop_orders_id'),
                dataIndex: 'o_id',
                filter: {
                    type : 'number'
                }
            },
            {
                text: t('coreshop_orders_orderNumber'),
                dataIndex: 'orderNumber',
                filter: {
                    type: 'string'
                }
            },
            {
                xtype : 'numbercolumn',
                align : 'right',
                text: t('coreshop_orders_total'),
                dataIndex: 'total',
                renderer: function (value, metaData, record) {
                    var currency = record.get('currency').symbol;

                    return coreshop.util.format.currency(currency, value);
                },

                filter: {
                    type : 'number'
                }
            },
            {
                text: t('coreshop_orders_orderState'),
                dataIndex: 'orderState',
                renderer : function (val) {
                    var store = pimcore.globalmanager.get('coreshop_orderstates');
                    var pos = store.findExact('id', val);
                    if (pos >= 0) {
                        var orderState = store.getAt(pos);
                        var bgColor = orderState.get('color');
                        var textColor = coreshop.helpers.constrastColor(bgColor);

                        return '<span class="rounded-color" style="background-color:' + bgColor + '; color: ' + textColor + '">' + orderState.get('name') + '</span>';
                    }

                    return null;
                },

                flex : 1,
                filter: {
                    type : 'list',
                    store : pimcore.globalmanager.get('coreshop_orderstates')
                }
            },
            {
                xtype : 'datecolumn',
                text: t('coreshop_orders_orderDate'),
                dataIndex: 'orderDate',
                format: t('coreshop_date_time_format'),
                filter: {
                    type : 'date'
                },
                width : 150
            },
            {
                menuDisabled: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: 50,
                items: [{
                    iconCls: 'pimcore_icon_open',
                    tooltip: t('open'),
                    handler: function (grid, rowIndex, colIndex) {
                        this.openOrder(grid.getStore().getAt(rowIndex));
                    }.bind(this)
                }]
            }
        ];

        if (coreshop.settings.multishop) {
            columns.splice(1, 0, {
                text: t('coreshop_shop'),
                dataIndex: 'shop',
                filter: {
                    type : 'number'
                },
                renderer : function (val) {
                    var store = pimcore.globalmanager.get('coreshop_shops');
                    var pos = store.findExact('id', String(val));
                    if (pos >= 0) {
                        var shop = store.getAt(pos);

                        return shop.get('name');
                    }

                    return null;
                }
            });
        }

        this.grid = Ext.create('Ext.grid.Panel', {
            title: t('coreshop_orders'),
            store: this.store,
            plugins: 'gridfilters',
            columns: columns,
            region: 'center',

            // paging bar on the bottom
            bbar: this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store),
            listeners : {
                select : function (grid, record) {
                    this.openOrder(record);
                }.bind(this)
            }
        });

        this.store.load();

        return this.grid;
    },

    openOrder : function (record) {
        coreshop.helpers.openOrder(record.get('o_id'));
    }
});
