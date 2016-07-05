/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.report.reports.customers');
pimcore.plugin.coreshop.report.reports.customers = Class.create(pimcore.plugin.coreshop.report.abstract, {

    url : '/plugin/CoreShop/admin_reports/get-customers-report',

    getName: function () {
        return t('coreshop_report_customers');
    },

    getIconCls: function () {
        return 'coreshop_icon_customer';
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
                        text: t('coreshop_report_customers_name'),
                        dataIndex : 'name',
                        flex : 1
                    },
                    {
                        text: t('coreshop_report_customers_count'),
                        dataIndex : 'count',
                        width : 50,
                        align : 'right'
                    },
                    {
                        text: t('coreshop_report_customers_sales'),
                        dataIndex : 'sales',
                        width : 100,
                        align : 'right'
                    }
                ]
            }
        });
    }
});
