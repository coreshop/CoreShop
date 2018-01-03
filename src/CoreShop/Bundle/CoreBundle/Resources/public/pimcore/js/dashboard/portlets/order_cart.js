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

pimcore.registerNS('pimcore.layout.portlets.coreshop_order_cart');
pimcore.layout.portlets.coreshop_order_cart = Class.create(pimcore.layout.portlets.abstract, {

    portletType: 'order_cart',

    getType: function () {
        return 'pimcore.layout.portlets.coreshop_order_cart';
    },

    getName: function () {
        return t('coreshop_portlet_orders_and_carts');
    },

    getIcon: function () {
        return 'pimcore_icon_portlet_modification_statistic';
    },

    getLayout: function (portletId) {
        this.store = new Ext.data.Store({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/portlet/get-data?portlet=' + this.portletType,
                extraParams: {
                    'filters[from]': new Date(new Date().getFullYear(), new Date().getMonth(), 1).getTime() / 1000,
                    'filters[to]': new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getTime() / 1000,
                    'store': this.config
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['timestamp', 'datetext', 'carts', 'orders']
        });

        this.store.load();

        var panel = new Ext.Panel({
            layout: 'fit',

            items: [{
                xtype: 'cartesian',
                height: 245,
                store: this.store,
                legend: {
                    docked: 'right'
                },
                interactions: ['itemhighlight',
                    {
                        type: 'panzoom',
                        zoomOnPanGesture: true
                    }
                ],
                axes: [{
                    type: 'numeric',
                    fields: ['carts', 'orders'],
                    position: 'left',
                    grid: true,
                    minimum: 0
                }, {
                    type: 'category',
                    fields: 'timestamp',
                    position: 'bottom'
                }
                ],
                series: [
                    {
                        type: 'line',
                        axis: ' left',
                        title: t('coreshop_cart'),
                        xField: 'timestamp',
                        yField: 'carts',
                        colors: ['#01841c'],
                        style: {
                            lineWidth: 2,
                            stroke: '#01841c'
                        },
                        marker: {
                            radius: 4,
                            fillStyle: '#01841c'
                        },
                        highlight: {
                            fillStyle: '#000',
                            radius: 5,
                            lineWidth: 2,
                            strokeStyle: '#fff'
                        },
                        tooltip: {
                            trackMouse: true,
                            style: 'background: #01841c',
                            renderer: function (tooltip, storeItem, item) {
                                var title = item.series.getTitle();
                                tooltip.setHtml(title + ' ' + t('coreshop_for') + ' ' + storeItem.get('datetext') + ': ' + storeItem.get(item.series.getYField()));
                            }
                        }
                    },
                    {
                        type: 'line',
                        axis: ' left',
                        title: t('coreshop_order'),
                        xField: 'timestamp',
                        yField: 'orders',
                        colors: ['#15428B'],
                        style: {
                            lineWidth: 2,
                            stroke: '#15428B'
                        },
                        marker: {
                            radius: 4,
                            fillStyle: '#15428B'
                        },
                        highlight: {
                            fillStyle: '#000',
                            radius: 5,
                            lineWidth: 2,
                            strokeStyle: '#fff'
                        },
                        tooltip: {
                            trackMouse: true,
                            style: 'background: #00bfff',
                            renderer: function (tooltip, storeItem, item) {
                                var title = item.series.getTitle();
                                tooltip.setHtml(title + ' ' + t('coreshop_for') + ' ' + storeItem.get('datetext') + ': ' + storeItem.get(item.series.getYField()));
                            }
                        }
                    }
                ]
            }]
        });

        var defaultConf = this.getDefaultConfig();
        defaultConf.tools = [
            {
                type: 'gear',
                handler: this.editSettings.bind(this)
            },
            {
                type: 'close',
                handler: this.remove.bind(this)
            }
        ];

        this.layout = Ext.create('Portal.view.Portlet', Object.extend(defaultConf, {
            title: this.getName(),
            iconCls: this.getIcon(),
            height: 275,
            layout: 'fit',
            items: [panel]
        }));

        this.layout.portletId = portletId;
        return this.layout;
    },

    editSettings: function () {

        var coreshopStore = pimcore.globalmanager.get('coreshop_stores');

        var win = new Ext.Window({
            width: 600,
            height: 150,
            modal: true,
            closeAction: 'destroy',
            items: [
                {
                    xtype: 'form',
                    bodyStyle: 'padding: 10px',
                    items: [
                        {
                            xtype: 'combo',
                            fieldLabel: t('coreshop_report_store'),
                            listWidth: 100,
                            width: 300,
                            store: coreshopStore,
                            displayField: 'name',
                            valueField: 'id',
                            forceSelection: true,
                            multiselect: false,
                            triggerAction: 'all',
                            name: 'coreshop_portlet_store',
                            id: 'coreshop_portlet_store',
                            queryMode: 'remote',
                            delimiter: false,
                            value: this.config,
                            listeners: {
                                afterrender: function () {
                                    var first;
                                    if (this.store.isLoaded()) {
                                        first = this.store.getAt(0);

                                        if (!this.getValue()) {
                                            this.setValue(first);
                                        }
                                    } else {
                                        this.store.load();

                                        if (!this.getValue()) {
                                            this.store.on('load', function (store, records, options) {
                                                first = store.getAt(0);
                                                this.setValue(first);
                                            }.bind(this));
                                        }
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: t('save'),
                            handler: function () {
                                var storeValue = Ext.getCmp('coreshop_portlet_store').getValue();
                                this.config = storeValue;
                                Ext.Ajax.request({
                                    url: '/admin/portal/update-portlet-config',
                                    params: {
                                        key: this.portal.key,
                                        id: this.layout.portletId,
                                        config: storeValue
                                    },
                                    success: function () {
                                        this.store.proxy.extraParams.store = storeValue;
                                        this.store.reload();
                                    }.bind(this)
                                });
                                win.close();
                            }.bind(this)
                        }
                    ]
                }
            ]
        });

        win.show();
    }
});
