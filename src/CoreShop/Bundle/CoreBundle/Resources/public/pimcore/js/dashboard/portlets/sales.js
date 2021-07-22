/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.layout.portlets.coreshop_sales');
pimcore.layout.portlets.coreshop_sales = Class.create(coreshop.portlet.abstract, {

    portletType: 'sales',

    getType: function () {
        return 'pimcore.layout.portlets.coreshop_sales';
    },

    getName: function () {
        return t('coreshop_portlet_sales');
    },

    getIcon: function () {
        return 'coreshop_carrier_costs_icon';
    },

    getFilterParams: function() {
        return {
            'from': new Date(new Date().getFullYear(), new Date().getMonth(), 1).getTime() / 1000,
            'to': new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getTime() / 1000,
            'store': this.config
        };
    },

    getLayout: function (portletId) {
        var me = this;

        this.store = new Ext.data.Store({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/portlet/get-data?portlet=' + this.portletType,
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['timestamp', 'datetext', 'sales']
        });
        this.store.on('beforeload', function (store, operation) {
            me.store.getProxy().setExtraParams(me.getFilterParams());
        });
        this.store.load();

        var panel = new Ext.Panel({
            layout: 'fit',
            height: 275,
            items: {
                xtype: 'cartesian',
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
                    fields: ['sales'],
                    position: 'left',
                    grid: true,
                    minimum: 0,
                    renderer: function(drawing, value, item) {
                        var factor = pimcore.globalmanager.get('coreshop.currency.decimal_factor');
                        return Ext.util.Format.number((value / factor));
                    }
                }, {
                    type: 'category',
                    fields: 'datetext',
                    position: 'bottom'
                }
                ],
                series: [
                    {
                        type: 'line',
                        axis: ' left',
                        title: t('coreshop_sales'),
                        xField: 'datetext',
                        yField: 'sales',
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
                                tooltip.setHtml(title + ' ' + t('coreshop_for') + ' ' + storeItem.get('datetext') + ': ' + storeItem.get('salesFormatted'));
                            }
                        }
                    }
                ]
            }
        });

        var defaultConf = this.getDefaultConfig();
        defaultConf.tools = [
            {
                type: 'gear',
                handler: this.editSettings.bind(this)
            },
            {
                type: 'download',
                handler: this.download.bind(this)
            },
            {
                type: 'close',
                handler: this.remove.bind(this)
            }
        ];

        this.layout = Ext.create('Portal.view.Portlet', Object.assign(defaultConf, {
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
                                    method: 'PUT',
                                    params: {
                                        key: this.portal.key,
                                        id: this.layout.portletId,
                                        config: storeValue
                                    },
                                    success: function () {
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
