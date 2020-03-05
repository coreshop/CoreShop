/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.cart.detail.blocks.detail');
coreshop.order.cart.detail.blocks.detail = Class.create(coreshop.order.cart.detail.blocks.detail, {
    // initBlock: function() {
    //     var me = this;
    //
    //     me.detailsStore = new Ext.data.JsonStore({
    //         data: []
    //     });
    //
    //     me.summaryStore = new Ext.data.JsonStore({
    //         data: []
    //     });
    //
    //     me.detailsInfo = Ext.create('Ext.panel.Panel', {
    //         title: t('coreshop_products'),
    //         border: true,
    //         margin: '0 0 20 0',
    //         iconCls: 'coreshop_icon_product',
    //         items: [],
    //         tools: [{
    //             type: 'coreshop-add',
    //             tooltip: t('add'),
    //             handler: function () {
    //                 pimcore.helpers.itemselector(
    //                     true,
    //                     function (products) {
    //                         products = products.map(function (pr) {
    //                             return {cartItem: {purchasable: pr.id, quantity: 1}};
    //                         });
    //
    //                         me.addProducts(products);
    //                     }.bind(this),
    //                     {
    //                         type: ['object'],
    //                         subtype: {
    //                             object: ['object', 'variant']
    //                         },
    //                         specific: {
    //                             classes: coreshop.stack.coreshop.purchasable
    //                         }
    //                     }
    //                 );
    //             }.bind(this)
    //         }]
    //     });
    // },
    //
    // generateGrid: function() {
    //     var me = this;
    //
    //     var actions = [
    //         {
    //             iconCls: 'pimcore_icon_open',
    //             tooltip: t('open'),
    //             handler: function (grid, rowIndex) {
    //                 var record = grid.getStore().getAt(rowIndex);
    //
    //                 pimcore.helpers.openObject(record.get('o_id'));
    //             }
    //         },
    //         {
    //             iconCls: 'pimcore_icon_delete',
    //             tooltip: t('delete'),
    //             handler: function (grid, rowIndex) {
    //                 var record = grid.getStore().getAt(rowIndex);
    //
    //                 me.removeProductFromCart(record);
    //             }
    //         }
    //     ];
    //
    //     var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    //         listeners: {
    //             beforeedit: function (editor, context) {
    //                 if (!me.sale.edit) {
    //                     return false;
    //                 }
    //             },
    //             validateedit: function (editor, context) {
    //                 if (context.field === 'quantity') {
    //                     return context.value >= 0;
    //                 }
    //
    //                 return true;
    //             },
    //             edit: function (editor, context, eOpts) {
    //                 if (context.originalValue !== context.value) {
    //                     itemsGrid.getView().refresh();
    //                 }
    //
    //                 this.reloadProducts();
    //             }.bind(this)
    //         }
    //     });
    //
    //     var itemsGrid = {
    //         xtype: 'grid',
    //         margin: '0 0 15 0',
    //         cls: 'coreshop-detail-grid',
    //         store: me.detailsStore,
    //         plugins: [cellEditing],
    //         columns: [
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'sku',
    //                 text: t('coreshop_sku'),
    //                 width: 100
    //             },
    //             {
    //                 xtype: 'gridcolumn',
    //                 flex: 1,
    //                 dataIndex: 'product_name',
    //                 text: t('coreshop_product')
    //             },
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'itemPriceNet',
    //                 width: 150,
    //                 align: 'right',
    //                 text: t('coreshop_price_without_tax'),
    //                 renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
    //             },
    //
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'itemPriceGross',
    //                 width: 150,
    //                 align: 'right',
    //                 text: t('coreshop_price_with_tax'),
    //                 renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
    //             },
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'quantity',
    //                 width: 100,
    //                 text: t('coreshop_quantity'),
    //                 field: {
    //                     xtype: 'numberfield',
    //                     decimalPrecision: 0
    //                 }
    //             },
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'totalNet',
    //                 width: 150,
    //                 align: 'right',
    //                 text: t('coreshop_total_without_tax'),
    //                 renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
    //             },
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'totalGross',
    //                 width: 150,
    //                 align: 'right',
    //                 text: t('coreshop_total'),
    //                 renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
    //             },
    //             {
    //                 menuDisabled: true,
    //                 sortable: false,
    //                 xtype: 'actioncolumn',
    //                 width: 50,
    //                 items: actions
    //             }
    //         ]
    //     };
    //
    //     itemsGrid = Ext.create(itemsGrid);
    //
    //     var summaryGrid = {
    //         xtype: 'grid',
    //         margin: '0 0 15 0',
    //         cls: 'coreshop-detail-grid',
    //         store: me.summaryStore,
    //         hideHeaders: true,
    //         columns: [
    //             {
    //                 xtype: 'gridcolumn',
    //                 flex: 1,
    //                 align: 'right',
    //                 dataIndex: 'key',
    //                 renderer: function (value, metaData, record) {
    //                     if (record.get("text")) {
    //                         return '<span style="font-weight:bold">' + record.get("text") + '</span>';
    //                     }
    //
    //                     return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
    //                 }
    //             },
    //             {
    //                 xtype: 'gridcolumn',
    //                 dataIndex: 'value',
    //                 width: 150,
    //                 align: 'right',
    //                 renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
    //             }
    //         ]
    //     };
    //
    //     this.detailsInfo.add(itemsGrid, summaryGrid);
    // },
    //
    // updateSale: function () {
    //     var me = this;
    //
    //     me.detailsStore.loadRawData(me.sale.details);
    //     me.summaryStore.loadRawData(me.sale.summary);
    //
    //     var tool = me.detailsInfo.tools.find(function(tool) { return tool.type === 'coreshop-add'; });;
    //
    //     if (me.sale.edit) {
    //         if (tool && Ext.isFunction(tool.show)) {
    //             tool.show();
    //         }
    //     } else {
    //         if (tool && Ext.isFunction(tool.hide)) {
    //             tool.hide();
    //         } else {
    //             tool.hidden = true;
    //         }
    //     }
    //
    //     me.detailsInfo.removeAll();
    //     this.generateGrid();
    // },
    //
    // addProducts: function(items) {
    //     this.panel.layout.setLoading(t("loading"));
    //
    //     Ext.Ajax.request({
    //         url: '/admin/coreshop/cart-edit/add-items',
    //         method: 'post',
    //         jsonData: {
    //             items: items,
    //             id: this.sale.o_id,
    //         },
    //         callback: function (request, success, response) {
    //             this.panel.layout.setLoading(false);
    //
    //             try {
    //                 response = Ext.decode(response.responseText);
    //
    //                 if (response.success) {
    //                     this.panel.reload();
    //                 } else {
    //                     Ext.Msg.alert(t('error'), response.message);
    //                 }
    //             }
    //             catch (e) {
    //                 Ext.Msg.alert(t('error'), e);
    //             }
    //         }.bind(this)
    //     });
    // },
    //
    // removeProductFromCart: function(item) {
    //     this.panel.layout.setLoading(t("loading"));
    //
    //     Ext.Ajax.request({
    //         url: '/admin/coreshop/cart-edit/remove-item',
    //         method: 'post',
    //         jsonData: {
    //             cartItem: item.get('o_id'),
    //             id: this.sale.o_id,
    //         },
    //         callback: function (request, success, response) {
    //             this.panel.layout.setLoading(false);
    //
    //             try {
    //                 response = Ext.decode(response.responseText);
    //
    //                 if (response.success) {
    //                     this.panel.reload();
    //                 } else {
    //                     Ext.Msg.alert(t('error'), response.message);
    //                 }
    //             }
    //             catch (e) {
    //                 Ext.Msg.alert(t('error'), e);
    //             }
    //         }.bind(this)
    //     });
    // },
    //
    // reloadProducts: function () {
    //     this.amendProducts(this.getItems(), true);
    // },
    //
    // getItems: function () {
    //     return this.detailsStore.getRange().map(function (record) {
    //         return {
    //             quantity: record.get('quantity')
    //         };
    //     });
    // },
    //
    // amendProducts: function (items, reset) {
    //     if (items.length <= 0) {
    //         return;
    //     }
    //
    //     this.panel.layout.setLoading(t("loading"));
    //
    //     Ext.Ajax.request({
    //         url: '/admin/coreshop/cart-edit/edit-items',
    //         method: 'post',
    //         jsonData: {
    //             items: items,
    //             id: this.sale.o_id,
    //         },
    //         callback: function (request, success, response) {
    //             this.panel.layout.setLoading(false);
    //
    //             try {
    //                 response = Ext.decode(response.responseText);
    //
    //                 if (response.success) {
    //                     this.panel.reload();
    //                 } else {
    //                     Ext.Msg.alert(t('error'), response.message);
    //                 }
    //             }
    //             catch (e) {
    //                 Ext.Msg.alert(t('error'), e);
    //             }
    //         }.bind(this)
    //     });
    // },
});
