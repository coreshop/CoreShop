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

pimcore.registerNS('coreshop.order.sale.create.step.totals');
coreshop.order.sale.create.step.totals = Class.create(coreshop.order.sale.create.abstractStep, {
    totalStore: null,

    initStep: function () {
        var me = this;

        me.eventManager.on('products.changed', function () {
            me.reloadTotalPanel();
        });
        me.eventManager.on('address.changed', function () {
            me.reloadTotalPanel();
        });
        me.eventManager.on('totals.reload', function () {
            me.reloadTotalPanel();
        });

        this.totalStore = new Ext.data.JsonStore({
            data: []
        });
    },

    reset: function() {
        this.layout.hide();
    },
    
    isValid: function (parent) {
        return true;
    },

    getPriority: function () {
        return 80;
    },

    getValues: function () {
        return [];
    },

    getPanel: function () {
        this.totalPanel = Ext.create('Ext.panel.Panel', {
            items: [
                {
                    xtype: 'grid',
                    store: this.totalStore,
                    hideHeaders: true,
                    margin: '0 0 20 0',
                    columns: [
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'key',
                            flex: 1,
                            renderer: function (value) {
                                return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                            }
                        },
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'value',
                            width: 150,
                            align: 'right',
                            renderer: function (value, metaData, record) {
                                return '<span style="font-weight:bold">' + record.get('valueFormatted') + '</span>';
                            }.bind(this)
                        }
                    ]
                },
            ]
        });

        return this.totalPanel;
    },

    getName: function () {
        return t('coreshop_order_create_totals');
    },

    getIconCls: function () {
        return 'coreshop_icon_orders';
    },

    getLayout: function ($super) {
        var layout = $super();

        layout.hide();

        return layout;
    },

    reloadTotalPanel: function () {
        var values = this.creationPanel.getValues();

        if (values.shippingAddress && values.invoiceAddress && values.products.length > 0) {
            this.layout.setLoading(t("loading"));

            Ext.Ajax.request({
                url: '/admin/coreshop/' + this.creationPanel.type + '-creation/get-totals',
                method: 'post',
                jsonData: values,
                callback: function (request, success, response) {
                    try {
                        response = Ext.decode(response.responseText);

                        if (response.success) {
                            this.totalStore.loadData(response.summary);
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

            this.layout.show();
        }
        else {
            this.layout.hide();
        }
    }
});