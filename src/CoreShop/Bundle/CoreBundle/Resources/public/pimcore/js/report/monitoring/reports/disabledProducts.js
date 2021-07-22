/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.report.monitoring.reports.disabledProducts');
coreshop.report.monitoring.reports.disabledProducts = Class.create(coreshop.report.monitoring.abstract, {

    url: '/admin/coreshop/reports/get-disabled-products-monitoring',

    getName: function () {
        return t('coreshop_monitoring_disableProducts');
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
                        text: t('coreshop_monitoring_disableProducts_enabled'),
                        dataIndex: 'enabled',
                        width: 100
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
