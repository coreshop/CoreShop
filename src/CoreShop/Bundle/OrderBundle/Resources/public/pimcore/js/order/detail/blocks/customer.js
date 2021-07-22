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

pimcore.registerNS('coreshop.order.order.detail.blocks.customer');
coreshop.order.order.detail.blocks.customer = Class.create(coreshop.order.order.detail.abstractBlock, {
    saleInfo: null,

    initBlock: function () {
        var me = this;

        me.customerInfo = Ext.create('Ext.panel.Panel', {
            border: true,
            flex: 6,
            iconCls: 'coreshop_icon_customer',
            tools: [
                {
                    type: 'coreshop-open',
                    tooltip: t('open'),
                    handler: function () {
                        if (me.sale.customer) {
                            pimcore.helpers.openObject(me.sale.customer.o_id);
                        }
                    }.bind(this)
                }
            ]
        });
    },

    getPriority: function () {
        return 10;
    },

    getPosition: function () {
        return 'right';
    },

    getPanel: function () {
        return this.customerInfo;
    },

    updateSale: function () {
        var me = this;

        var guestStr = me.sale.customer.user ? ' –  ' + t('coreshop_is_guest') : '';
        me.customerInfo.setTitle(t('coreshop_customer') + ': ' + (me.sale.customer ? me.sale.customer.firstname + ' (' + me.sale.customer.o_id + ')' : t('unknown')) + guestStr);
        me.customerInfo.removeAll();

        var items = [];

        if (me.sale.customer) {
            if (!me.sale.customer.user) {

                items.push({
                    xtype: 'panel',
                    bodyPadding: 10,
                    margin: '0 0 10px 0',
                    style: {
                        borderStyle: 'solid',
                        borderColor: '#ccc',
                        borderRadius: '5px',
                        borderWidth: '1px'
                    },
                    items: [
                        {
                            xtype: 'label',
                            style: 'font-weight:bold;display:block',
                            text: t('email')
                        },
                        {
                            xtype: 'label',
                            style: 'display:block',
                            text: me.sale.customer.email
                        },
                        {
                            xtype: 'label',
                            style: 'font-weight:bold;display:block',
                            text: t('coreshop_customer_created')
                        },
                        {
                            xtype: 'label',
                            style: 'display:block',
                            text: Ext.Date.format(new Date(me.sale.customer.o_creationDate * 1000), t('coreshop_date_time_format'))
                        }
                    ]
                });
            }
        }

        if (me.sale.comment) {
            items.push({
                xtype: 'panel',
                bodyPadding: 10,
                margin: '0 0 10px 0',
                style: {
                    borderStyle: 'solid',
                    borderColor: '#ccc',
                    borderRadius: '5px',
                    borderWidth: '1px'
                },
                items: [
                    {
                        xtype: 'label',
                        style: 'font-weight:bold;display:block',
                        text: t('coreshop_comment')
                    },
                    {
                        xtype: 'label',
                        style: 'display:block',
                        html: Ext.util.Format.nl2br(me.sale.comment)
                    }
                ]
            });
        }

        items.push({
            xtype: 'tabpanel',
            items: [
                me.getAddressPanelForAddress(me.sale.address.shipping, t('coreshop_address_shipping'), 'shipping'),
                me.getAddressPanelForAddress(me.sale.address.billing, t('coreshop_address_invoice'), 'invoice')
            ]
        });

        me.customerInfo.add(items);
    },

    getAddressPanelForAddress: function (address, title, type) {
        var me = this,
            country = pimcore.globalmanager.get("coreshop_countries").getById(address.country);

        var panel = {
            xtype: 'panel',
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    '->',
                    {
                        iconCls: 'coreshop_icon_open',
                        text: t('open'),
                        handler: function () {
                            pimcore.helpers.openObject(address.o_id);
                        }
                    }
                ]
            }],
            title: title,
            layout: {
                type: 'hbox',
                align: 'stretch'
            },
            height: 220,
            items: [
                {
                    xtype: 'panel',
                    bodyPadding: 5,
                    html: address.formatted,
                    flex: 1
                }
            ]
        };

        if (pimcore.settings.google_maps_api_key) {
            panel.items.push({
                xtype: 'panel',
                html: '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap'
                + '&center=' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + country.get("name")
                + '&markers=color:blue|' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + country.get("name")
                + '&key=' + pimcore.settings.google_maps_api_key
                + '" />',
                flex: 1,
                bodyPadding: 5
            });
        }

        return panel;
    }
});
