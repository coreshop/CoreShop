/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.create.step.products');
coreshop.order.sale.create.step.products = Class.create(coreshop.order.sale.create.abstractStep, {

    initStep: function () {
        var me = this;

        me.eventManager.on('currency.changed', function () {
            me.reloadProducts();
        });
        me.eventManager.on('store.changed', function () {
            me.reloadProducts();
        });
    },

    isValid: function () {
        var values = this.getValues();

        return values.products.length > 0;
    },

    getPriority: function () {
        return 30;
    },

    getValues: function () {
        return {
            products: this.getCartProducts()
        };
    },

    reset: function() {
        this.cartPanelStore.setData([]);
    },

    getPanel: function () {
        var me = this;
        var modelName = 'CoreShopCreateOrderCart';
        if (!Ext.ClassManager.isCreated(modelName)) {
            Ext.define(modelName, {
                extend: 'Ext.data.Model',
                idProperty: 'o_id'
            });
        }

        this.cartPanelStore = new Ext.data.JsonStore({
            data: [],
            model: modelName
        });

        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            listeners: {
                edit: function (editor, context, eOpts) {
                    if (context.originalValue !== context.value) {
                        this.cartPanelGrid.getView().refresh();
                    }

                    this.reloadProducts();
                }.bind(this)
            }
        });

        this.cartPanelGrid = Ext.create('Ext.grid.Panel', {
            margin: '0 0 15 0',
            cls: 'coreshop-detail-grid',
            store: this.cartPanelStore,
            plugins: [cellEditing],
            columns: [
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'o_id',
                    text: t('id'),
                    width: 100
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'name',
                    flex: 1,
                    text: t('name'),
                    renderer: function (value, metaData, record) {
                        if (Object.keys(record.get("localizedfields").data).indexOf(pimcore.settings.language) > 0)
                            return record.get("localizedfields").data[pimcore.settings.language].name;
                        else {
                            var keys = Object.keys(record.get("localizedfields").data);

                            if (keys.length > 0) {
                                return record.get("localizedfields").data[keys[0]].name;
                            }
                        }

                        return "";
                    }.bind(this)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'price',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_base_price'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + record.get('priceFormatted') + '</span>';
                    }.bind(this)
                    /*field : { TODO: Make price editable
                        xtype: 'numberfield',
                        decimalPrecision : 2
                    }*/
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'priceConverted',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_price'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + record.get('priceConvertedFormatted') + '</span>';
                    }.bind(this)
                    /*field : { TODO: Make price editable
                        xtype: 'numberfield',
                        decimalPrecision : 2
                    }*/
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
                    text: t('coreshop_base_total'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + record.get('totalFormatted') + '</span>';
                    }.bind(this)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'totalConverted',
                    width: 150,
                    align: 'right',
                    text: t('coreshop_total'),
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + record.get('totalConvertedFormatted') + '</span>';
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

                                pimcore.helpers.openObject(record.get('o_id'));
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
        });

        return this.cartPanelGrid;
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
                                return {id: pr.id, quantity: 1};
                            });

                            this.addProductsToCart(products);
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

    removeProductFromCart: function(product) {
        this.cartPanelStore.remove(product);

        this.eventManager.fireEvent('products.changed');
        this.eventManager.fireEvent('validation');
    },

    addProductsToCart: function (products, reset) {
        if (products.length <= 0) {
            return;
        }

        this.layout.setLoading(t("loading"));

        var values = this.creationPanel.getValues();
        values['products'] = products;

        Ext.Ajax.request({
            url: '/admin/coreshop/' + this.creationPanel.type + '-creation/get-product-details',
            method: 'post',
            jsonData: values,
            callback: function (request, success, response) {
                try {
                    response = Ext.decode(response.responseText);

                    if (response.success) {
                        if (reset) {
                            this.cartPanelStore.removeAll();
                        }

                        this.cartPanelStore.add(response.products);

                        this.eventManager.fireEvent('products.changed');
                        this.eventManager.fireEvent('validation');
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                }
                catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }

                this.layout.setLoading(false);
            }.bind(this)
        });
    },

    reloadProducts: function () {
        var baseStep = this.creationPanel.getStep('base');

        if (baseStep.isValid()) {
            this.addProductsToCart(this.getCartProducts(), true);
        }
    },

    getCartProducts: function () {
        return this.cartPanelStore.getRange().map(function (record) {
            return {
                id: record.get('o_id'),
                quantity: record.get('quantity')
            }
        });
    },

    getName: function () {
        return t('coreshop_order_create_products');
    },

    getIconCls: function () {
        return 'coreshop_icon_cart';
    }
});