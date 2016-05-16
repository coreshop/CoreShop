/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
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
        var itemsPerPage = 30;

        this.store = new Ext.data.JsonStore({
            remoteSort: true,
            remoteFilter: true,
            autoDestroy: true,
            autoSync: true,
            pageSize: itemsPerPage,
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

        this.grid = Ext.create('Ext.grid.Panel', {
            title: t('coreshop_orders'),
            store: this.store,
            plugins: 'gridfilters',
            columns: [
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
            ],
            region: 'center',

            // paging bar on the bottom
            bbar: this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store, itemsPerPage),
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
