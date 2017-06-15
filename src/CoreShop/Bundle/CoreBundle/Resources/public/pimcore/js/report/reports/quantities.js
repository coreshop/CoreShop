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

pimcore.registerNS('coreshop.report.reports.quantities');
coreshop.report.reports.quantities = Class.create(coreshop.report.abstract, {

    url: '/admin/coreshop/reports/get-quantities-report',
    remoteSort: true,

    getName: function () {
        return t('coreshop_report_quantities');
    },

    getIconCls: function () {
        return 'coreshop_icon_quantity';
    },

    getStoreFields: function () {
        return [
            {name: 'retailPrice', type: 'number'},
            {name: 'totalPrice', type: 'number'},
            {name: 'quantity', type: 'integer'}
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
                        text: t('coreshop_report_quantities_quantity'),
                        dataIndex: 'quantity',
                        width: 100,
                        align: 'right'
                    },
                    {
                        text: t('coreshop_report_quantities_price'),
                        dataIndex: 'retailPrice',
                        width: 100,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('retailPriceFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_quantities_totalPrice'),
                        dataIndex: 'totalPrice',
                        width: 100,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('totalPriceFormatted');
                        }
                    }
                ],
                bbar: pimcore.helpers.grid.buildDefaultPagingToolbar(this.getStore())
            }
        });
    },

    getFilterFields: function () {
        return [];
    },

    getFilterParams: function () {
        return {};
    }
});
