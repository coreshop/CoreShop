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

pimcore.registerNS('coreshop.report.reports.products');
coreshop.report.reports.products = Class.create(coreshop.report.abstract, {

    url: '/admin/coreshop/reports/get-products-report',

    getName: function () {
        return t('coreshop_report_products');
    },

    getIconCls: function () {
        return 'coreshop_icon_product';
    },

    getStoreFields: function () {
        return [
            {name: 'sales', type: 'number'},
            {name: 'salesPrice', type: 'number'},
            {name: 'count', type: 'integer'},
            {name: 'profit', type: 'number'}
        ];
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
                        text: t('name'),
                        dataIndex: 'name',
                        flex: 1
                    },
                    {
                        text: t('coreshop_report_products_count'),
                        dataIndex: 'count',
                        width: 50,
                        align: 'right'
                    },
                    {
                        text: t('coreshop_report_products_salesPrice'),
                        dataIndex: 'salesPrice',
                        width: 100,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesPriceFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_products_sales'),
                        dataIndex: 'sales',
                        width: 100,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_products_profit'),
                        dataIndex: 'profit',
                        width: 100,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('profitFormatted');
                        }
                    }
                ]
            }
        });
    }
});
