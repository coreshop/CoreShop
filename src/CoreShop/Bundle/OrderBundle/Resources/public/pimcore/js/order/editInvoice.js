/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.order.order.editInvoice');
coreshop.order.order.editInvoice = {

    showWindow: function (invoice, currency, callback) {
        var window = new Ext.window.Window({
            width: 600,
            height: 450,
            resizeable: false,
            modal: true,
            layout: 'fit',
            items: [{
                xtype: 'form',
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: t('OK'),
                        handler: function (btn) {
                            window.close();
                            window.destroy();
                        },
                        iconCls: 'pimcore_icon_apply'
                    }
                ],
                items: [
                    {
                        xtype: 'datefield',
                        format: 'd.m.Y H:i',
                        altFormats: 'U',
                        fieldLabel: t('coreshop_date'),
                        name: 'date',
                        disabled: true,
                        value: invoice.get('invoiceDate')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_invoice_number'),
                        disabled: true,
                        value: invoice.get('invoiceNumber')
                    },
                    {
                        xtype: 'textfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_total_without_tax'),
                        disabled: true,
                        value: invoice.get('totalNet') / pimcore.globalmanager.get('coreshop.currency.decimal_factor'),
                        renderer: coreshop.util.format.currency.bind(this, currency.isoCode)
                    },
                    {
                        xtype: 'textfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_total'),
                        disabled: true,
                        value: invoice.get('totalGross') / pimcore.globalmanager.get('coreshop.currency.decimal_factor'),
                        renderer: coreshop.util.format.currency.bind(this, currency.isoCode)
                    },
                    {
                        xtype: 'button',
                        fieldLabel: '',
                        style: 'margin: 5px 0;',
                        tooltip: t('open'),
                        handler: function (widgetColumn) {
                            pimcore.helpers.openObject(invoice.get('id'), 'object');

                            window.close();
                        },
                        listeners: {
                            beforerender: function (widgetColumn) {
                                widgetColumn.setText(Ext.String.format(t('coreshop_open_order_invoice'), invoice.get('invoiceNumber')));
                            }
                        }
                    },
                    {
                        xtype: 'gridpanel',
                        title: t('details'),
                        store: new Ext.data.JsonStore({
                            data: invoice.get('items'),
                            fields: ['_itemName', 'quantity']
                        }),
                        columns: [
                            {text: 'Item', dataIndex: '_itemName', flex: 2 },
                            {text: 'Quantity', dataIndex: 'quantity', flex: 1 }
                        ]
                    }
                ]
            }]
        });

        window.show();
    }

};
