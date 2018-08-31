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

pimcore.registerNS('coreshop.order.sale.detail.blocks.detail');
coreshop.order.sale.detail.blocks.detail = Class.create(coreshop.order.sale.detail.abstractBlock, {

    initBlock: function () {
        var me = this;

        me.detailsStore = new Ext.data.JsonStore({
            data: []
        });

        me.summaryStore = new Ext.data.JsonStore({
            data: []
        });

        me.detailsInfo = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_products'),
            border: true,
            margin: '0 0 20 0',
            iconCls: 'coreshop_icon_product',
        });
    },

    getPriority: function () {
        return 10;
    },

    getPosition: function () {
        return 'bottom';
    },

    getPanel: function () {
        return this.detailsInfo;
    },

    updateSale: function () {
        var me = this;

        me.detailsStore.loadRawData(me.sale.details);
        me.summaryStore.loadRawData(me.sale.summary);

        me.detailsInfo.removeAll();

        var actions = [
            {
                iconCls: 'pimcore_icon_open',
                tooltip: t('open'),
                handler: function (grid, rowIndex) {
                    var record = grid.getStore().getAt(rowIndex);

                    pimcore.helpers.openObject(record.get('o_id'));
                }
            }
        ];

        var itemsGrid = {
            xtype: 'grid',
            margin: '0 0 15 0',
            cls: 'coreshop-detail-grid',
            store: me.detailsStore,
            columns: [
                {
                    xtype: 'gridcolumn',
                    flex: 1,
                    dataIndex: 'product_name',
                    text: t('coreshop_product')
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'wholesale_price',
                    text: t('coreshop_wholesale_price'),
                    width: 150,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(me, me.sale.currency.symbol)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'price_without_tax',
                    text: t('coreshop_price_without_tax'),
                    width: 150,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(me, me.sale.currency.symbol),
                    field: {
                        xtype: 'numberfield',
                        decimalPrecision: 4
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'price',
                    text: t('coreshop_price_with_tax'),
                    width: 150,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(me, me.sale.currency.symbol)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'amount',
                    text: t('coreshop_quantity'),
                    width: 150,
                    align: 'right',
                    field: {
                        xtype: 'numberfield',
                        decimalPrecision: 0
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'total',
                    text: t('coreshop_total'),
                    width: 150,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(me, me.sale.currency.symbol)
                },
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 50,
                    items: actions
                }
            ]
        };

        var summaryGrid = {
            xtype: 'grid',
            margin: '0 0 15 0',
            cls: 'coreshop-detail-grid',
            store: me.summaryStore,
            hideHeaders: true,
            columns: [
                {
                    xtype: 'gridcolumn',
                    flex: 1,
                    align: 'right',
                    dataIndex: 'key',
                    renderer: function (value, metaData, record) {
                        if (record.get("text")) {
                            return '<span style="font-weight:bold">' + record.get("text") + '</span>';
                        }

                        return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'value',
                    width: 150,
                    align: 'right',
                    renderer: function (value, metaData, record) {
                        return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.currency.symbol, value) + '</span>';
                    }
                }
            ]
        };

        var detailItems = [itemsGrid, summaryGrid];

        if (me.sale.priceRule) {
            var priceRuleStore = new Ext.data.JsonStore({
                data: me.sale.priceRule
            });

            var priceRuleItem = {
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: priceRuleStore,
                hideHeaders: true,
                title: t('coreshop_pricerules'),
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        align: 'right',
                        dataIndex: 'name'
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'discount',
                        width: 150,
                        align: 'right',
                        renderer: function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(me.sale.currency.symbol, value) + '</span>';
                        }
                    }
                ]
            };

            detailItems.splice(1, 0, priceRuleItem);
        }

        me.detailsInfo.add(detailItems);
    }
});