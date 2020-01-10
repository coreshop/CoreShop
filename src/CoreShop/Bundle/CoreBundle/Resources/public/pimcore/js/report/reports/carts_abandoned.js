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

pimcore.registerNS('coreshop.report.reports.carts_abandoned');
coreshop.report.reports.carts_abandoned = Class.create(coreshop.report.abstractStore, {

    reportType: 'carts_abandoned',

    getName: function () {
        return t('coreshop_report_carts_abandoned');
    },

    getIconCls: function () {
        return 'coreshop_icon_report_carts_abandoned';
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
                        text: t('coreshop_report_user_name'),
                        dataIndex: 'userName',
                        flex: 2
                    },
                    {
                        text: t('coreshop_report_user_email'),
                        dataIndex: 'email',
                        flex: 2
                    },
                    {
                        text: t('coreshop_report_selected_payment'),
                        dataIndex: 'selectedPayment',
                        flex: 2
                    },
                    {
                        text: t('coreshop_report_creation_date'),
                        dataIndex: 'creationDate',
                        flex: 2,
                        renderer: function (val) {
                            if (val) {
                                return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                            }
                            return '';
                        }
                    },
                    {
                        text: t('coreshop_report_modifiction_date'),
                        dataIndex: 'modificationDate',
                        flex: 2,
                        renderer: function (val) {
                            if (val) {
                                return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                            }
                            return '';
                        }
                    },
                    {
                        text: t('coreshop_report_items_in_cart'),
                        dataIndex: 'itemsInCart',
                        flex: 2
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        flex: 1,
                        items: [{
                            iconCls: 'pimcore_icon_open',
                            tooltip: t('open'),
                            handler: function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);
                                pimcore.helpers.openObject(record.get('cartId'));
                            }
                        }]
                    }
                ]
            }
        });
    }
});
