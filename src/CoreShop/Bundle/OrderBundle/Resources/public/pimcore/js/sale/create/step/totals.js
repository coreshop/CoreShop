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

pimcore.registerNS('coreshop.order.sale.create.step.totals');
coreshop.order.sale.create.step.totals = Class.create(coreshop.order.sale.create.abstractStep, {
    totalStore: null,

    initStep: function () {
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

    setPreviewData: function(data) {
        this.totalStore.loadData(data.summary);

        if (data.shippingAddress && data.invoiceAddress && data.items.length > 0) {
            this.layout.show();
        }
        else {
            this.layout.hide();
        }
    },

    getPriority: function () {
        return 100;
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
    }
});
