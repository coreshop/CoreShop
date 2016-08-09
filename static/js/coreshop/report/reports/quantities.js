/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.report.reports.quantities');
pimcore.plugin.coreshop.report.reports.quantities = Class.create(pimcore.plugin.coreshop.report.abstract, {

    url : '/plugin/CoreShop/admin_reports/get-quantities-report',

    getName: function () {
        return t('coreshop_report_quantities');
    },

    getIconCls: function () {
        return 'coreshop_icon_quantity';
    },

    getGrid : function () {
        return new Ext.Panel({
            layout:'fit',
            height: 275,
            items: {
                xtype : 'grid',
                store: this.getStore(),
                columns : [
                    {
                        text: t('coreshop_report_quantities_name'),
                        dataIndex : 'name',
                        flex : 1
                    },
                    {
                        text: t('coreshop_report_quantities_quantity'),
                        dataIndex : 'quantity',
                        width : 100,
                        align : 'right'
                    },
                    {
                        text: t('coreshop_report_quantities_price'),
                        dataIndex : 'price',
                        width : 100,
                        align : 'right'
                    },
                    {
                        text: t('coreshop_report_quantities_totalPrice'),
                        dataIndex : 'totalPrice',
                        width : 100,
                        align : 'right'
                    }
                ]
            }
        });
    },

    getFilterFields : function () {
        return [];
    },

    getFilterParams : function () {
        return {};
    }
});
