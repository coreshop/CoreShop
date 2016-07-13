/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.product.grid');
pimcore.plugin.coreshop.product.grid = Class.create({

    layoutId : 'coreshop_products',

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
                title: t('coreshop_product_list'),
                iconCls: 'coreshop_icon_product_list',
                border: false,
                layout: 'border',
                closable: true,
                items: this.getItems()
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
                url: '/plugin/CoreShop/admin_product/get-products',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    totalProperty : 'total'
                }
            },

            //alternatively, a Ext.data.Model name can be given (see Ext.data.Store for an example)
            fields: [
                'o_id',
                'name',
                'quantity',
                { name : 'price', type : 'float' }
            ]
        });

        var columns = [
            {
                text: t('coreshop_product_id'),
                dataIndex: 'o_id',
                filter: 'number'
            },
            {
                text: t('coreshop_product_name'),
                dataIndex: 'name',
                filter: {
                    type: 'string'
                },
                flex : 1
            },
            {
                text: t('coreshop_product_quantity'),
                dataIndex: 'quantity',
                filter: 'number'
            },
            {
                xtype : 'numbercolumn',
                align : 'right',
                text: t('coreshop_product_price'),
                dataIndex: 'price',
                renderer: coreshop.util.format.currency.bind(this, '€'),
                filter: 'number'
            }
        ];

        if(coreshop.settings.multishop) {
            columns.splice(1, 0, {
                text: t('coreshop_shop'),
                dataIndex: 'shops',
                filter: {
                    type : 'list',
                    store : pimcore.globalmanager.get('coreshop_shops')
                },
                renderer : function(val) {
                    var store = pimcore.globalmanager.get('coreshop_shops');
                    var storeString = "";

                    Ext.each(val, function(shop) {
                        var pos = store.findExact('id', String(shop));
                        if (pos >= 0) {
                            var shopObj = store.getAt(pos);

                            storeString += shopObj.get("name") + " ";
                        }
                    });

                    return storeString;
                }
            });
        }

        this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store);

        this.grid = Ext.create('Ext.grid.Panel', {
            title: t('coreshop_product_list'),
            store: this.store,
            plugins: 'gridfilters',
            columns: columns,
            region: 'center',

            // paging bar on the bottom
            bbar: this.pagingtoolbar,
            listeners : {
                itemclick : this.openProduct
            }
        });

        this.store.load();

        return this.grid;
    },

    openProduct : function (grid, record, item, index, e, eOpts) {
        pimcore.helpers.openObject(record.get('o_id'));
    }
});
