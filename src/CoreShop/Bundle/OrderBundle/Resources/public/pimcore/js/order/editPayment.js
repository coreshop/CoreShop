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

pimcore.registerNS('coreshop.order.order.editPayment');
coreshop.order.order.editPayment = {

    showWindow: function (payment, callback) {
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
                        value: payment.get('datePayment')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_payment_number'),
                        disabled: true,
                        value: payment.get('paymentNumber')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_paymentProvider'),
                        disabled: true,
                        value: payment.get('provider')
                    },
                    {
                        xtype: 'numberfield',
                        name: 'total_amount',
                        fieldLabel: t('coreshop_amount'),
                        disabled: true,
                        value: payment.get('amount') / pimcore.globalmanager.get('coreshop.currency.decimal_factor')
                    },
                    {
                        xtype: 'gridpanel',
                        title: t('details'),
                        viewConfig: {
                        enableTextSelection: true
                        },
                        features: [{
                            ftype: 'rowbody',
                            getAdditionalData: function(data, idx, record, orig) {
                                return {
                                    rowBody: '<div>' + record.get('detail') + '</div>',
                                    rowBodyCls: record.get('detail') === null ? 'x-hidden' : ''
                                };
                            }
                        }],
                        store: new Ext.data.Store({
                            data: payment.get('details'),
                            fields: ['name', 'value', 'detail']
                        }),
                        columns: [
                            {
                                text: 'Name',
                                dataIndex: 'name',
                                flex: 1
                            },
                            {
                                text: 'Value',
                                dataIndex: 'value',
                                flex: 2,
                                renderer: function arg(val){
                                    return '<div style="white-space: normal;">' + val + '</div>';
                                }
                            }
                        ]
                    }
                ]
            }]
        });

        window.show();
    }

};
