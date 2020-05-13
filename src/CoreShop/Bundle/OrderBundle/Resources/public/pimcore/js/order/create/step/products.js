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

pimcore.registerNS('coreshop.order.order.create.step.products');
coreshop.order.order.create.step.products = Class.create(coreshop.order.order.create.abstractStep, {

    products: [],

    initStep: function () {
        this.products = [];
    },

    isValid: function () {
        var values = this.getValues();

        return values.items.length > 0;
    },

    getPriority: function () {
        return 30;
    },

    reset: function() {
        this.products = [];
        this.cartPanelStore.setData([]);
    },

    getPanel: function () {
        var me = this;
        var modelName = 'CoreShopCreateOrderCart';
        if (!Ext.ClassManager.isCreated(modelName)) {
            Ext.define(modelName, {
                extend: 'Ext.data.Model'
            });
        }

        this.cartPanelStore = new Ext.data.JsonStore({
            data: [],
            model: modelName
        });

        this.cartPanelGrid = Ext.create(this.generateItemGrid());
        this.cartPanelGrid.on('edit', function (editor, context, eOpts) {
            this.onRowEditingFinished(editor, context, eOpts);
        }.bind(this));

        this.cartPanelGrid.on('beforeedit', function (editor, context, eOpts) {
            context.record.set('customItemPrice', context.record.get('customItemPrice') / 100);
        }.bind(this));

        return this.cartPanelGrid;
    },

    onRowEditingFinished: function(editor, context, eOpts) {
        var qty = editor.editor.form.findField('quantity');
        var customItemPrice = editor.editor.form.findField('customItemPrice');
        var discount = editor.editor.form.findField('customItemDiscount');

        context.record.set('quantity', qty.getValue());
        context.record.set('customItemPrice', customItemPrice.getValue() * 100);
        context.record.set('customItemDiscount', discount.getValue());

        this.products = this.getCartProducts();

        this.reloadProducts();
    },

    generateItemGrid: function() {
        var me = this;

        return {
            xtype: 'grid',
            margin: '0 0 15 0',
            minHeight: 300,
            cls: 'coreshop-detail-grid',
            store: this.cartPanelStore,
            plugins: [Ext.create('Ext.grid.plugin.RowEditing')],
            columns: [
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'product',
                    text: t('id'),
                    width: 100
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'productName',
                    flex: 1,
                    text: t('name'),
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'customItemDiscount',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_custom_item_discount'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + record.get('customItemDiscount') + '</span>';
                    }.bind(this),
                    field : {
                        xtype: 'numberfield',
                        decimalPrecision : 0,
                        minValue: 0,
                        maxValue: 100
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'customItemPrice',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_custom_item_price'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.baseCurrency.iso, value) + '</span>';
                    }.bind(this),
                    field : {
                        xtype: 'numberfield',
                        decimalPrecision : 2
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'price',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_price'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.baseCurrency.iso, value) + '</span>';
                    }.bind(this)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'convertedPrice',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_converted_price'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.currency.iso, value) + '</span>';
                    }.bind(this)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'quantity',
                    width: 100,
                    text: t('coreshop_quantity'),
                    field: {
                        xtype: 'numberfield',
                        decimalPrecision: 0
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'total',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_total'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.baseCurrency.iso, value) + '</span>';
                    }.bind(this)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'convertedTotal',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_converted_total'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.currency.iso, value) + '</span>';
                    }.bind(this)
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 50,
                    items: [
                        {
                            iconCls: 'pimcore_icon_open',
                            tooltip: t('open'),
                            handler: function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);

                                pimcore.helpers.openObject(record.get('product'));
                            }
                        },
                        {
                            iconCls: 'pimcore_icon_delete',
                            tooltip: t('delete'),
                            handler: function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);

                                me.removeProductFromCart(record);
                            }
                        }
                    ]
                }
            ]
        };
    },

    getTools: function () {
        return [
            {
                type: 'coreshop-add-product',
                tooltip: t('add'),
                handler: function () {
                    pimcore.helpers.itemselector(
                        true,
                        function (products) {
                            products = products.map(function (pr) {
                                return {product: pr.id, quantity: 1};
                            });

                            this.addProducts(products);
                        }.bind(this),
                        {
                            type: ['object'],
                            subtype: {
                                object: ['object', 'variant']
                            },
                            specific: {
                                classes: coreshop.stack.coreshop.product
                            }
                        }
                    );
                }.bind(this)
            }
        ];
    },

    getValues: function () {
        return {
            items: this.getCartProducts()
        };
    },

    getPreviewValues: function () {
        return {
            items: this.products
        };
    },

    setPreviewData: function(data) {
        this.cartPanelStore.removeAll();

        this.products = data.items;
        this.sale = data;
        this.cartPanelStore.add(data.items);
    },

    removeProductFromCart: function(product) {
        this.cartPanelStore.remove(product);
        this.products = this.getCartProducts();

        this.eventManager.fireEvent('preview');
    },

    addProducts: function(products) {
        var merged = this.products;

        Array.prototype.push.apply(merged, products);

        this.products = merged;
        this.eventManager.fireEvent('preview');
    },

    reloadProducts: function () {
        this.eventManager.fireEvent('preview');
    },

    getCartProducts: function () {
        return this.cartPanelStore.getRange().map(function (record) {
            return record.data;
        });
    },

    getName: function () {
        return t('coreshop_order_create_products');
    },

    getIconCls: function () {
        return 'coreshop_icon_cart';
    }
});
