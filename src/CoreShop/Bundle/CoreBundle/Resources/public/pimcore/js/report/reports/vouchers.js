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

pimcore.registerNS('coreshop.report.reports.vouchers');
coreshop.report.reports.vouchers = Class.create(coreshop.report.abstractStore, {

    reportType: 'vouchers',

    getName: function () {
        return t('coreshop_report_vouchers');
    },

    getIconCls: function () {
        return 'coreshop_icon_report_vouchers';
    },

    getFromStartDate: function () {
        var d = new Date();
        d.setMonth(d.getMonth() - 2);
        return d;
    },

    getToStartDate: function () {
        var d = new Date();
        d.setDate(d.getDate() - 2);
        return d;
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
                        text: t('coreshop_report_voucher_code'),
                        dataIndex: 'code',
                        flex: 3
                    },
                    {
                        text: t('coreshop_report_voucher_discount'),
                        dataIndex: 'discount',
                        flex: 2
                    },
                    {
                        text: t('coreshop_report_voucher_pricerule'),
                        dataIndex: 'rule',
                        flex: 2
                    },
                    {
                        text: t('coreshop_report_voucher_applied_date'),
                        dataIndex: 'usedDate',
                        flex: 2,
                        renderer: function (val) {
                            if (val) {
                                return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                            }
                            return '';
                        }
                    }
                ]
            }
        });
    }
});
