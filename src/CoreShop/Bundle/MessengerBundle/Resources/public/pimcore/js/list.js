/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.messenger.list');
coreshop.messenger.list = Class.create({
    chartStore: null,

    receiverName: null,
    failedReceiverName: null,

    messagesStore: null,
    failedMessagesStore: null,

    autoRefreshInterval: null,
    autoRefreshTimer: null,
    lastRefreshLabel: null,

    initialize: function () {
        this.getPanel();
    },

    reload: function () {
        this.chartStore.reload();

        if (this.receiverName) {
            this.messagesStore.reload();
        }

        if (this.failedReceiverName) {
            this.failedMessagesStore.reload();
        }
    },

    updateLastRefreshLabel: function () {
        if (this.lastRefreshLabel) {
            var now = new Date();
            var timeString = Ext.Date.format(now, 'Y-m-d H:i:s');
            this.lastRefreshLabel.setText(t('coreshop_messenger_last_refresh') + ': ' + timeString);
        }
    },

    startAutoRefresh: function (interval) {
        this.stopAutoRefresh();

        if (interval > 0) {
            this.autoRefreshInterval = interval;
            this.autoRefreshTimer = setInterval(this.reload.bind(this), interval * 1000);
        }
    },

    stopAutoRefresh: function () {
        if (this.autoRefreshTimer) {
            clearInterval(this.autoRefreshTimer);
            this.autoRefreshTimer = null;
        }
        this.autoRefreshInterval = null;
    },

    getPanel: function () {
        if (!this.panel) {
            this.lastRefreshLabel = Ext.create('Ext.toolbar.TextItem', {
                text: ''
            });

            var autoRefreshStore = Ext.create('Ext.data.Store', {
                fields: ['value', 'text'],
                data: [
                    {value: 0, text: t('coreshop_messenger_auto_refresh_disabled')},
                    {value: 5, text: t('coreshop_messenger_auto_refresh_5s')},
                    {value: 10, text: t('coreshop_messenger_auto_refresh_10s')},
                    {value: 30, text: t('coreshop_messenger_auto_refresh_30s')}
                ]
            });

            this.panel = Ext.create('Ext.panel.Panel', {
                id: 'coreshop_messenger_list',
                title: t('coreshop_messenger_list'),
                iconCls: 'coreshop_icon_messenger',
                border: false,
                layout: 'fit',
                closable: true,
                tbar: [{
                    xtype: 'button',
                    iconCls: 'pimcore_icon_reload',
                    handler: this.reload.bind(this)
                }, '-', {
                    xtype: 'combo',
                    store: autoRefreshStore,
                    displayField: 'text',
                    valueField: 'value',
                    value: 0,
                    editable: false,
                    width: 200,
                    queryMode: 'local',
                    listeners: {
                        select: function (combo, record) {
                            this.startAutoRefresh(record.get('value'));
                        }.bind(this)
                    }
                }, '->', this.lastRefreshLabel],
                items: [{
                    xtype: 'panel',
                    layout: 'border',
                    bodyBorder: false,
                    defaults: {
                        collapsible: false,
                        split: false,
                    },
                    items: [{
                        region: 'north',
                        height: 200,
                        items: this.getChart()
                    }, {
                        region: 'center',
                        xtype: 'tabpanel',
                        items: [
                            this.getFailedGrid(),
                            this.getGrid(),
                        ]
                    }]
                }]
            });

            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('coreshop_messenger_list');

            this.panel.on('destroy', function () {
                this.stopAutoRefresh();
                pimcore.globalmanager.remove('coreshop_messenger_list');
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getChart: function () {
        this.chartStore = new Ext.data.Store({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_admin_messenger_count'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['receiver', 'count'],
            listeners: {
                load: this.updateLastRefreshLabel.bind(this)
            }
        });
        this.chartStore.load();

        return {
            xtype: 'cartesian',
            store: this.chartStore,
            height: 200,
            axes: [{
                type: 'numeric',
                position: 'left',
                adjustByMajorUnit: true,
                grid: true,
                fields: ['count'],
                minimum: 0
            }, {
                type: 'category',
                position: 'bottom',
                grid: true,
                fields: ['receiver'],
                label: {
                    renderer: function (value) {
                        if (value && value.length > 15) {
                            return Ext.String.ellipsis(value, 15);
                        }
                        return value;
                    }
                }
            }],
            series: [{
                type: 'bar',
                title: 'Messages',
                xField: 'receiver',
                yField: 'count',
                label: {
                    field: 'count',
                    display: 'insideEnd'
                },
                tooltip: {
                    trackMouse: true,
                    renderer: function (tooltip, record) {
                        tooltip.setHtml(record.get('receiver') + ': ' + record.get('count') + ' ' + t('coreshop_messenger_messages'));
                    }
                }
            }]
        };
    },

    getFailedGrid: function () {
        var failureReceivers = Ext.create('Ext.form.ComboBox', {
            xtype: 'combo',
            fieldLabel: t('coreshop_messenger_failure_receivers'),
            width: 400,
            mode: 'local',
            store: {
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('coreshop_admin_messenger_list_failure_receivers'),
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
            },
            displayField: 'receiver',
            valueField: 'receiver',
            forceSelection: true,
            triggerAction: 'all',
            allowBlank: false,
            listeners: {
                change: function (oldVal, newVal) {
                    if (newVal) {
                        this.failedMessagesStore.proxy.url = Routing.generate('coreshop_admin_messenger_list_failed', {receiverName: newVal});
                        this.failedMessagesStore.load();

                        grid.enable();

                        this.failedReceiverName = newVal;
                    } else {
                        grid.disable();
                        this.failedMessagesStore.clear();

                        this.failedReceiverName = null;
                    }
                }.bind(this)
            }
        });

        this.failedMessagesStore = new Ext.data.Store({
            autoDestroy: true,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_admin_messenger_list_failed'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['id', 'class', 'failedAt', 'error']
        });

        var grid = new Ext.grid.Panel({
            xtype: 'grid',
            layout: 'fit',
            store: this.failedMessagesStore,
            viewConfig: {
                enableTextSelection: true
            },
            tbar: [
                failureReceivers
            ],
            columns: [{
                text: 'ID',
                width: 75,
                dataIndex: 'id'
            }, {
                text: t('coreshop_messenger_class'),
                width: 400,
                dataIndex: 'class'
            }, {
                text: t('coreshop_messenger_failed_at'),
                width: 100,
                formatter: 'date("m/d/Y")',
                dataIndex: 'failed_at'
            }, {
                text: t('coreshop_messenger_error'),
                flex: 1,
                dataIndex: 'error'
            }, {
                xtype: 'actioncolumn',
                width: 100,
                menuDisabled: true,
                sortable: false,
                items: [{
                    iconCls: 'pimcore_icon_info',
                    tooltip: t('coreshop_messenger_info'),
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        new Ext.Window({
                            width: 500,
                            height: 550,
                            title: t('info'),
                            modal: true,
                            layout: 'fit',
                            items: [{
                                padding: 10,
                                scrollable: true,
                                html: record.data.serialized
                            }]
                        }).show();
                    }
                }, {
                    iconCls: 'pimcore_icon_error',
                    tooltip: t('coreshop_messenger_info'),
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        new Ext.Window({
                            width: 500,
                            height: 550,
                            title: t('error'),
                            modal: true,
                            layout: 'fit',
                            items: [{
                                padding: 10,
                                scrollable: true,
                                html: record.data.error
                            }]
                        }).show();
                    }
                }, {
                    iconCls: 'pimcore_icon_delete',
                    tooltip: t('coreshop_messenger_delete_failed_message'),
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        grid.setLoading(t('loading'));

                        Ext.Ajax.request({
                            url: Routing.generate('coreshop_admin_messenger_delete', {receiverName: this.failedReceiverName}),
                            method: 'DELETE',
                            params: {
                                id: record.data.id,
                            },
                            success: function () {
                                grid.setLoading(false);
                                grid.getStore().reload();

                                this.chartStore.reload();
                            }.bind(this)
                        });
                    }.bind(this)
                }, {
                    iconCls: 'pimcore_icon_apply',
                    tooltip: t('coreshop_messenger_retry_failed_message'),
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        grid.setLoading(t('loading'));

                        Ext.Ajax.request({
                            url: Routing.generate('coreshop_admin_messenger_retry', {receiverName: this.failedReceiverName}),
                            method: 'POST',
                            params: {
                                id: record.data.id
                            },
                            success: function () {
                                grid.setLoading(false);
                                grid.getStore().reload();

                                this.chartStore.reload();
                            }.bind(this)
                        });
                    }.bind(this)
                }]
            }],
        });

        return {
            layout: 'fit',
            items: [grid],
            title: t('coreshop_messenger_failed_messages'),
        };
    },


    getGrid: function () {
        var receivers = Ext.create('Ext.form.ComboBox', {
            xtype: 'combo',
            fieldLabel: t('coreshop_messenger_receivers'),
            width: 400,
            mode: 'local',
            store: {
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('coreshop_admin_messenger_list_receivers'),
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
            },
            displayField: 'receiver',
            valueField: 'receiver',
            forceSelection: true,
            triggerAction: 'all',
            allowBlank: false,
            listeners: {
                change: function (oldVal, newVal) {
                    if (newVal) {
                        this.messagesStore.proxy.url = Routing.generate('coreshop_admin_messenger_list', {receiverName: newVal});
                        this.messagesStore.load();

                        grid.enable();

                        this.receiverName = newVal;
                    } else {
                        grid.disable();
                        this.messagesStore.clear();

                        this.receiverName = null;
                    }
                }.bind(this)
            }
        });

        this.messagesStore = new Ext.data.Store({
            autoDestroy: true,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_admin_messenger_list'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['id', 'class']
        });

        var grid = new Ext.grid.Panel({
            xtype: 'grid',
            layout: 'fit',
            store: this.messagesStore,
            viewConfig: {
                enableTextSelection: true
            },
            tbar: [
                receivers
            ],
            columns: [{
                text: 'ID',
                width: 150,
                dataIndex: 'id'
            }, {
                text: t('coreshop_messenger_class'),
                flex: 1,
                dataIndex: 'class'
            }, {
                xtype: 'actioncolumn',
                width: 80,
                menuDisabled: true,
                sortable: false,
                items: [{
                    iconCls: 'pimcore_icon_info',
                    tooltip: t('coreshop_messenger_info'),
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        new Ext.Window({
                            width: 500,
                            height: 550,
                            title: t('info'),
                            modal: true,
                            layout: "fit",
                            items: [{
                                padding: 10,
                                html: record.data.serialized
                            }]
                        }).show();
                    }
                }]
            }],
        });

        return {
            layout: 'fit',
            title: t('coreshop_messenger_pending_messages'),
            items: [grid]
        };
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem('coreshop_messenger_list');
    },
});
