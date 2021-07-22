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

pimcore.registerNS('coreshop.report.reports.products');
coreshop.report.reports.products = Class.create(coreshop.report.abstractStore, {

    reportType: 'products',

    getName: function () {
        return t('coreshop_report_products');
    },

    getIconCls: function () {
        return 'coreshop_icon_product';
    },

    getObjectTypeField: function () {
        return this.panel.down('[name=objectType]');
    },

    getFilterParams: function ($super) {
        var fields = $super();
        fields.objectType = this.getObjectTypeField().getValue();
        return fields;
    },

    showPaginator: function () {
        return true;
    },

    getStoreFields: function () {
        return [
            {name: 'name', type: 'string'},
            {name: 'productName', type: 'string'},
            {name: 'orderCount', type: 'integer'},
            {name: 'quantityCount', type: 'integer'},
            {name: 'sales', type: 'number'},
            {name: 'salesPrice', type: 'number'},
            {name: 'profit', type: 'number'}
        ];
    },

    getDocketItemsForPanel: function ($super) {

        var fields = $super();

        fields.push(
            {
                xtype: 'toolbar',
                dock: 'top',
                items: this.getAdditionalFilterFields()
            }
        );

        return fields;

    },
    getAdditionalFilterFields: function () {

        var fields = [];

        fields.push({
            xtype: 'combo',
            fieldLabel: t('coreshop_report_products_types'),
            name: 'objectType',
            value: 'all',
            width: 350,
            store: [
                ['all', t('coreshop_report_products_types_all')],
                ['object', t('coreshop_report_products_types_objects')],
                ['variant', t('coreshop_report_products_types_variants')],
                ['container', t('coreshop_report_products_types_container')]
            ],
            triggerAction: 'all',
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: 'local',
            listeners: {
                change: function (combo, value) {
                    this.panel.down('[name=objectTypeDescription]').setHidden(value !== 'container');
                }.bind(this)
            }
        });

        fields.push({
            xtype: 'label',
            name: 'objectTypeDescription',
            style: '',
            hidden: true,
            height: 40,
            html: t('coreshop_report_products_types_container_description')
        });

        return fields;
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
                        dataIndex: 'productName',
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
                        text: t('coreshop_report_products_salesPrice'),
                        dataIndex: 'salesPrice',
                        flex: 1,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesPriceFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_products_sales'),
                        dataIndex: 'sales',
                        flex: 1,
                        align: 'right',
                        renderer: function (value, metadata, record) {
                            return record.get('salesFormatted');
                        }
                    },
                    {
                        text: t('coreshop_report_products_profit'),
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
