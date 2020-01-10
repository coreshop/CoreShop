/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.report.reports.manufacturer');
coreshop.report.reports.manufacturer = Class.create(coreshop.report.abstractStore, {

    reportType: 'manufacturer',

    getName: function () {
        return t('coreshop_report_manufacturer');
    },

    getIconCls: function () {
        return 'coreshop_icon_report_manufacturer';
    },

    getStoreFields: function () {
        return [
            {name: 'name', type: 'string'},
            {name: 'manufacturerName', type: 'string'},
            {name: 'orderCount', type: 'integer'},
            {name: 'quantityCount', type: 'integer'},
            {name: 'sales', type: 'number'},
            {name: 'profit', type: 'number'}
        ];
    },

    showPaginator: function () {
        return true;
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
                        dataIndex: 'manufacturerName',
                        flex: 3,
                        renderer: function (value, metadata, record) {
                            return record.get('name');
                        }
                    },
                    {
                        text: t('coreshop_report_products_order_count'),
                        dataIndex: 'orderCount',
                        flex: 1,
                        align: 'right'
                    },
                    {
                        text: t('coreshop_report_products_quantity_count'),
                        dataIndex: 'quantityCount',
                        flex: 1,
                        align: 'right'
                    },
                    {
                        text: t('coreshop_report_manufacturer_sales'),
                        dataIndex: 'sales',
                        flex: 1,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_manufacturer_profit'),
                        dataIndex: 'profit',
                        flex: 1,
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
