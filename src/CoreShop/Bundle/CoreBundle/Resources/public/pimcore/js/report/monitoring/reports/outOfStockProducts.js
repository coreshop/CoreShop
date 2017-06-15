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

pimcore.registerNS('coreshop.report.monitoring.reports.outOfStockProducts');
coreshop.report.monitoring.reports.outOfStockProducts = Class.create(coreshop.report.monitoring.abstract, {

    url: '/admin/coreshop/reports/get-out-of-stock-products-monitoring',

    getName: function () {
        return t('coreshop_monitoring_outOfStockProducts');
    },

    getIconCls: function () {
        return 'coreshop_icon_product';
    },

    getGrid: function () {
        return new Ext.Panel({
            layout: 'fit',
            height: 275,
            items: {
                xtype: 'grid',
                store: this.getStore(),
                columns: [
                    {
                        text: t('id'),
                        dataIndex: 'id',
                        width: 100
                    },
                    {
                        text: t('name'),
                        dataIndex: 'name',
                        flex: 1
                    },
                    {
                        text: t('coreshop_monitoring_outOfStockProducts_quantity'),
                        dataIndex: 'quantity',
                        width: 100
                    },
                    {
                        text: t('coreshop_monitoring_outOfStockProducts_out_of_stock_behaviour'),
                        dataIndex: 'outOfStockBehaviour',
                        width: 100,
                        renderer: function (value) {
                            return t('coreshop_stock_' + value + '_order');
                        }
                    }
                ],
                listeners: {
                    rowclick: function (grid, record) {
                        var d = record.data;

                        pimcore.helpers.openObject(d.id, 'object');
                    }
                }
            }
        });
    }
});
