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

pimcore.registerNS('coreshop.order.sale.list');
coreshop.order.sale.list = Class.create({

    type: '',
    grid: null,
    store: null,
    columns: [],
    storeFields: [],

    initialize: function () {
        this.panels = [];

        Ext.Ajax.request({
            url: '/admin/coreshop/'+this.type+'/get-grid-configuration',
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
        tabPanel.setActiveItem('coreshop_' + this.type);
    },


    prepareConfig: function (columnConfig) {
        var me = this,
            gridColumns = [],
            storeModelFields = [];

        Ext.each(columnConfig, function (column) {
            var newColumn = column;
            var storeModelField = {
                name: column.dataIndex,
                type: column.type
            };

            newColumn.id = me.type + '_' + newColumn.dataIndex;
            newColumn.text = newColumn.text.split('|').map(function (string) {
                //text like [foo bar] won't be translated. just remove brackets.
                return string.match(/\[([^)]+)]/) ? string.replace(/\[|]/gi, '') : t(string);
            }).join(' ');

            if (newColumn.hasOwnProperty('renderAs')) {
                if (Ext.isFunction(this[newColumn.renderAs + 'Renderer'])) {
                    newColumn.renderer = this[newColumn.renderAs + 'Renderer'];
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
        var bgColor = orderStateInfo.color,
            textColor = coreshop.helpers.constrastColor(bgColor);
        return '<span class="rounded-color" style="background-color:' + bgColor + '; color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    orderShippingStateRenderer: function (orderStateInfo) {
        var bgColor = coreshop.helpers.hexToRgb(orderStateInfo.color),
            textColor = 'black';
        return '<span class="rounded-color" style="background-color: rgba(' + bgColor.join(',') + ', 0.2); color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    orderPaymentStateRenderer: function (orderStateInfo) {
        var bgColor = coreshop.helpers.hexToRgb(orderStateInfo.color),
            textColor = 'black';
        return '<span class="rounded-color" style="background-color: rgba(' + bgColor.join(',') + ', 0.2); color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    orderInvoiceStateRenderer: function (orderStateInfo) {
        var bgColor = coreshop.helpers.hexToRgb(orderStateInfo.color),
            textColor = 'black';
        return '<span class="rounded-color" style="background-color: rgba(' + bgColor.join(',') + ', 0.2); color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    storeRenderer: function (val) {
        var stores = pimcore.globalmanager.get('coreshop_stores');
        var store = stores.getById(val);
        if (store) {
            return store.get('name');
        }

        return null;
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: 'coreshop_' + this.type,
                title: t('coreshop_' + this.type),
                iconCls: 'coreshop_icon_'+this.type+'s',
                border: false,
                layout: 'border',
                closable: true,
                items: this.getItems(),
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            iconCls: 'coreshop_icon_'+this.type+'_create',
                            text: t('coreshop_'+this.type+'_create'),
                            handler: function () {
                                new coreshop.order[this.type].create.panel();
                            }.bind(this)
                        }
                    ]
                }]
            });

            // add event listener
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove('coreshop_' + this.type);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem('coreshop_' + this.type);

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
                url: '/admin/coreshop/'+this.type+'/list',
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
            title: t('coreshop_'+this.type+'s'),
            store: this.store,
            plugins: 'gridfilters',
            columns: this.columns,
            region: 'center',
            stateful: true,
            stateId: 'coreshop_'+this.type+'_grid',
            // paging bar on the bottom
            bbar: this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store),
            listeners: {
                itemclick: function (grid, record) {
                    grid.setLoading(t('loading'));

                    this.open(record, function () {
                        grid.setLoading(false);
                    }.bind(this));
                }.bind(this)
            }
        });

        return this.grid;
    },

    open: function (record, callback) {
        coreshop.order.helper.openSale(record.get('o_id'), this.type, callback);
    }
});
