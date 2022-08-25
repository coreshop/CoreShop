/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.report.reports.categories');
coreshop.report.reports.categories = Class.create(coreshop.report.abstractStore, {

    reportType: 'categories',

    getName: function () {
        return t('coreshop_report_categories');
    },

    getIconCls: function () {
        return 'coreshop_icon_category';
    },

    getStoreFields: function () {
        return [
            {name: 'name', type: 'string'},
            {name: 'categoryName', type: 'string'},
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
                        dataIndex: 'categoryName',
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
                        text: t('coreshop_report_categories_sales'),
                        dataIndex: 'sales',
                        flex: 1,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_categories_profit'),
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
    },

    getOrderStateField: function () {
        return this.panel.down('[name=orderState]');
    },

    getFilterParams: function ($super) {
        var fields = $super();
        fields.orderState = JSON.stringify(this.getOrderStateField().getValue());
        return fields;
    },

    getDocketItemsForPanel: function ($super) {

        var fields = $super();

        fields.push(
            {
                xtype: 'toolbar',
                dock: 'top',
                layout: {
                    type: 'vbox',
                    align: 'stretch',
                    pack: 'start',
                },
                items: this.getAdditionalFilterFields()
            }
        );

        return fields;

    },
    getAdditionalFilterFields: function () {
        var fields = [];
        fields.push({
            xtype: 'tagfield',
            fieldLabel: t('coreshop_condition_orderState'),
            name: 'orderState',
            value: ['all'],
            width: 350,
            store: pimcore.globalmanager.get('coreshop_states_order'),
            displayField: 'label',
            valueField: 'state',
            triggerAction: 'all',
            filterPickList: true,
            minChars: 1,
            typeAhead: true,
            editable: true,
            forceSelection: true,
            queryMode: 'local'
        });

        return fields;
    }
});
