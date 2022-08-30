/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.report.abstract');
coreshop.report.abstract = Class.create(pimcore.report.abstract, {

    reportType: 'abstract',
    remoteSort: false,

    matchType: function (type) {
        var types = ['global'];
        return !!pimcore.report.abstract.prototype.matchTypeValidate(type, types);
    },

    getName: function () {
        return 'coreshop';
    },

    getIconCls: function () {
        return 'coreshop_icon_report';
    },

    getGrid: function () {
        return false;
    },

    getStoreField: function () {
        return this.panel.down('[name=store]');
    },

    getFromField: function () {
        return this.panel.down('[name=from]');
    },

    getToField: function () {
        return this.panel.down('[name=to]');
    },

    getFromStartDate: function () {
        return new Date(new Date().getFullYear(), 0, 1);
    },

    getToStartDate: function () {
        return new Date(new Date().getFullYear(), 11, 31);
    },

    showPaginator: function () {
        return false;
    },

    getDocketItemsForPanel: function () {

        return [
            {
                xtype: 'toolbar',
                dock: 'top',
                items: this.getFilterFields()
            }
        ];
    },

    getPanel: function () {

        var grid;

        if (!this.panel) {

            var bbar = null;

            if (this.showPaginator() !== false) {
                bbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.getStore());
            }

            this.panel = new Ext.Panel({
                title: this.getName(),
                layout: 'fit',
                border: false,
                items: [],
                bbar: bbar,
                dockedItems: this.getDocketItemsForPanel()
            });

            grid = this.getGrid();

            if (grid) {
                this.panel.add(grid);
            }

            this.filter();
        }

        return this.panel;
    },

    getFilterFields: function () {
        var _ = this;

        return [
            {
                xtype: 'button',
                text: t('coreshop_report_day'),
                flex: 1,
                handler: function () {
                    var today = new Date();
                    var yesterday = new Date();

                    yesterday.setDate(today.getDate() - 1);

                    this.getFromField().setValue(yesterday);
                    this.getToField().setValue(today);

                    this.filter();
                }.bind(this)
            },
            {
                xtype: 'button',
                text: t('coreshop_report_month'),
                flex: 1,
                handler: function () {
                    var now = new Date();

                    this.getFromField().setValue(new Date(now.getFullYear(), now.getMonth(), 1));
                    this.getToField().setValue(new Date(now.getFullYear(), now.getMonth() + 1, 0));

                    this.filter();
                }.bind(this)
            },
            {
                xtype: 'button',
                text: t('coreshop_report_year'),
                flex: 1,
                handler: function () {
                    var now = new Date();

                    this.getFromField().setValue(new Date(now.getFullYear(), 0, 1));
                    this.getToField().setValue(new Date(now.getFullYear(), 11, 31));

                    this.filter();
                }.bind(this)
            },
            {
                xtype: 'button',
                text: t('coreshop_report_day_minus'),
                flex: 1,
                handler: function () {
                    var today = new Date();
                    var yesterday = new Date();

                    today.setDate(today.getDate() - 1);
                    yesterday.setDate(today.getDate() - 1);

                    this.getFromField().setValue(yesterday);
                    this.getToField().setValue(today);

                    this.filter();
                }.bind(this)
            },
            {
                xtype: 'button',
                text: t('coreshop_report_month_minus'),
                flex: 1,
                handler: function () {
                    var now = new Date();

                    this.getFromField().setValue(new Date(now.getFullYear(), now.getMonth() - 1, 1));
                    this.getToField().setValue(new Date(now.getFullYear(), now.getMonth(), 0));

                    this.filter();
                }.bind(this)
            },
            {
                xtype: 'button',
                text: t('coreshop_report_year_minus'),
                flex: 1,
                handler: function () {
                    var now = new Date();

                    this.getFromField().setValue(new Date(now.getFullYear() - 1, 0, 1));
                    this.getToField().setValue(new Date(now.getFullYear() - 1, 11, 31));

                    this.filter();
                }.bind(this)
            },
            '->',
            {
                xtype: 'datefield',
                fieldLabel: t('coreshop_report_year_from'),
                flex: 3,
                name: 'from',
                labelWidth: false,
                labelStyle: 'width: 70px;',
                value: this.getFromStartDate()
            },
            {
                xtype: 'datefield',
                fieldLabel: t('coreshop_report_year_to'),
                flex: 3,
                name: 'to',
                labelWidth: false,
                labelStyle: 'width: 70px;',
                value: this.getToStartDate()
            },
            {
                xtype: 'button',
                flex: 1,
                text: t('coreshop_report_filter'),
                handler: function () {
                    this.filter();
                }.bind(this)
            }
            ,
            {
                xtype: 'button',
                flex: 1,
                text: t('coreshop_report_export'),
                iconCls: 'pimcore_icon_download',
                handler: function () {
                    this.download();
                }.bind(this)
            }
        ];
    },

    filter: function () {
        this.getStore().load();
    },

    download: function () {
        var me = this;

        var options = {};
        this.getStore().setLoadOptions(options);

        var operation = this.getStore().createOperation('read', options);
        var request = this.getStore().getProxy().buildRequest(operation);

        var filterParams = request.getParams();
        filterParams['report'] = me.reportType;

        pimcore.helpers.download(Routing.generate('coreshop_admin_report_export', filterParams));
    },

    getStore: function () {
        if (!this.store) {
            var me = this,
                fields = ['timestamp', 'text', 'data'];

            if (Ext.isFunction(this.getStoreFields)) {
                fields = Ext.apply(fields, this.getStoreFields());
            }

            this.store = new Ext.data.Store({
                autoDestroy: true,
                remoteSort: this.remoteSort,
                pageSize: 50,
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('coreshop_admin_report_get_data', { report: this.reportType }),
                    actionMethods: {
                        read: 'GET'
                    },
                    reader: {
                        type: 'json',
                        rootProperty: 'data',
                        totalProperty: 'total'
                    }
                },
                fields: fields
            });

            this.store.on('beforeload', function (store, operation) {
                store.getProxy().setExtraParams(me.getFilterParams());
            });
        }

        return this.store;
    },

    getFilterParams: function () {
        return {
            'from': this.getFromField().getValue().getTime() / 1000,
            'to': this.getToField().getValue().getTime() / 1000
        };
    }
});

