/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.report.reports.customers');
coreshop.report.reports.customers = Class.create(coreshop.report.abstract, {

    reportType: 'customers',

    getName: function () {
        return t('coreshop_report_customers');
    },

    getIconCls: function () {
        return 'coreshop_icon_customer';
    },

    getStoreFields: function () {
        return [
            {name: 'emailAddress', type: 'string'},
            {name: 'orderCount', type: 'integer'},
            {name: 'sales', type: 'number'}
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
                        text: t('email'),
                        dataIndex: 'emailAddress',
                        flex: 3
                    },
                    {
                        text: t('coreshop_report_customers_count'),
                        dataIndex: 'orderCount',
                        flex: 1,
                        align: 'right'
                    },
                    {
                        text: t('coreshop_report_customers_sales'),
                        dataIndex: 'sales',
                        flex: 1,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesFormatted');
                        }
                    }
                ]
            }
        });
    }
});
