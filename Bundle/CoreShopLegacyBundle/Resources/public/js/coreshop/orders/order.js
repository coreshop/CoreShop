/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.orders.order');
pimcore.plugin.coreshop.orders.order = Class.create({

    order : null,
    objectData : null,
    layoutId : null,

    borderStyle : {
        borderStyle: 'solid',
        borderColor: '#ccc',
        borderRadius: '5px',
        borderWidth : '1px'
    },

    initialize: function (order) {
        this.order = order;
        this.layoutId = 'coreshop_order_' + this.order.o_id;

        this.getLayout();
        this.getObjectInfo();
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    reload : function () {
        this.layout.destroy();
        coreshop.helpers.openOrder(this.order.o_id);
    },

    getObjectInfo : function () {

        Ext.Ajax.request({
            url: '/admin/object/get/',
            params: {id: this.order.o_id},
            success: function(response) {
                try {
                    this.objectData = Ext.decode(response.responseText);
                    this.setWorkflowInfo();

                } catch (e) { }
            }.bind(this)
        });
    },

    getLayout: function () {
        if (!this.layout) {

            var buttons = [{
                iconCls : 'pimcore_icon_reload',
                text : t('reload'),
                handler : function () {
                    this.reload();
                }.bind(this)
            }];

            if(this.order.invoiceCreationAllowed) {
                buttons.push({
                    iconCls : 'coreshop_icon_orders_invoice',
                    text : t('coreshop_invoice_create_short'),
                    handler : function () {
                        this.createInvoice();
                    }.bind(this)
                });
            }

            if(this.order.shipmentCreationAllowed) {
                buttons.push({
                    iconCls : 'coreshop_icon_orders_shipment',
                    text : t('coreshop_shipment_create_short'),
                    handler : function () {
                        this.createShipment();
                    }.bind(this)
                });
            }

            // create new panel
            this.layout = new Ext.panel.Panel({
                id: this.layoutId,
                title: t('coreshop_order') + ': ' + this.order.orderNumber,
                iconCls: 'coreshop_icon_orders',
                border: false,
                layout: 'border',
                scrollable: 'y',
                closable: true,
                items: this.getItems(),
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: buttons
                }]
            });

            // add event listener
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove(this.layoutId);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getItems: function () {
        return [this.getPanel()];
    },

    getPanel : function () {
        var defaults = {
            style: this.borderStyle,
            cls : 'coreshop-panel',
            bodyPadding : 5
        };

        var leftItems = [
            this.getOrderInfo(),
            this.getCarrierAndPaymentDetails(),
            this.getShipmentDetails(),
            this.getInvoiceDetails(),
            this.getPaymentDetails(),
            this.getMailDetails()
        ];

        var visitorInfo = this.getVisitorInfo();

        if(visitorInfo) {
            leftItems.push(visitorInfo);
        }

        var rightItems = [
            this.getCustomerInfo(),
            this.getMessagesInfo()
        ];

        var contentItems = [
            {
                xtype : 'container',
                border : 0,
                style : {
                    border : 0
                },
                flex : 7,
                defaults : defaults,
                items : leftItems
            },
            {
                xtype : 'container',
                border : 0,
                style : {
                    border : 0
                },
                flex : 5,
                defaults : defaults,
                items : rightItems
            }
        ];

        var items = [
            this.getHeader(),
            {
                xtype : 'container',
                layout : 'hbox',
                margin : '0 0 20 0',
                border : 0,
                style: {
                    border : 0
                },
                items : contentItems
            }
        ];

        var paymentPluginPanel = this.getPaymentPluginInfo();
        var pluginPanel = this.getPluginInfo();

        if (pluginPanel) {
            items.push(pluginPanel);
        }

        if (paymentPluginPanel) {
            items.push(paymentPluginPanel);
        }

        items.push(this.getDetailInfo());

        this.panel = Ext.create('Ext.container.Container', {
            border : false,
            items : items,
            padding : 20,
            region : 'center',
            defaults : defaults
        });

        return this.panel;
    },

    getHeader : function () {
        if (!this.headerPanel) {
            var items = [
                {
                    xtype : 'panel',
                    html : t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(this.order.orderDate * 1000), t('coreshop_date_time_format')) + '</span>',
                    bodyPadding : 20,
                    flex : 1
                },
                {
                    xtype : 'panel',
                    html : t('coreshop_orders_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(this.order.currency.symbol, this.order.total) + '</span>',
                    bodyPadding : 20,
                    flex : 1
                },
                {
                    xtype : 'panel',
                    html : t('coreshop_messaging_messages') + '<br/><span class="coreshop_order_big">' + this.order.unreadMessages + '</span>', //TODO: Add Messages
                    bodyPadding : 20,
                    flex : 1
                },
                {
                    xtype : 'panel',
                    html : t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + this.order.items.length + '</span>',
                    bodyPadding : 20,
                    flex : 1
                }
            ];

            if (coreshop.settings.multishop) {
                items.push({
                    xtype : 'panel',
                    html : t('coreshop_shop') + '<br/><span class="coreshop_order_big">' + this.order.shop.name + '</span>',
                    bodyPadding : 20,
                    flex : 1
                });
            }

            this.headerPanel = Ext.create('Ext.panel.Panel', {
                layout : 'hbox',
                margin : '0 0 20 0',
                items : items
            });
        }

        return this.headerPanel;
    },

    getOrderInfo : function () {
        if (!this.orderInfo) {
            this.orderStatesStore = new Ext.data.JsonStore({
                data : this.order.statesHistory
            });

            this.orderInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_order') + ': ' + this.order.orderNumber + ' (' + this.order.o_id + ')',
                margin : '0 20 20 0',
                border : true,
                flex : 8,
                iconCls : 'coreshop_icon_orders',
                tools : [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler : function () {
                            pimcore.helpers.openObject(this.order.o_id);
                        }.bind(this)
                    }
                ],
                items : [
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-detail-grid',
                        store : this.orderStatesStore,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'title',
                                text : t('coreshop_orderstate'),
                                renderer : function (value, metaData) {

                                    if (value) {
                                        var bgColor = '';
                                        //@fixme: add some colored circle to the left instead of heavy color stuff!
                                        return '<span class="rounded-color" style="background-color:' + bgColor + ';"></span>' + value;
                                    }

                                    return '';
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                width : 250,
                                dataIndex : 'date',
                                text : t('date')
                            }
                        ]
                    }
                ]
            });
        }

        return this.orderInfo;
    },

    getCustomerInfo : function () {
        if (!this.customerInfo) {
            var items = [

            ];

            if(this.order.customer) {
                if (!this.order.customer.isGuest) {

                    items.push({
                        xtype: 'panel',
                        bodyPadding: 10,
                        margin: '0 0 10px 0',
                        style: this.borderStyle,
                        items: [
                            {
                                xtype: 'label',
                                style: 'font-weight:bold;display:block',
                                text: t('email')
                            },
                            {
                                xtype: 'label',
                                style: 'display:block',
                                text: this.order.customer.email
                            },
                            {
                                xtype: 'label',
                                style: 'font-weight:bold;display:block',
                                text: t('coreshop_customer_created')
                            },
                            {
                                xtype: 'label',
                                style: 'display:block',
                                text: Ext.Date.format(new Date(this.order.customer.o_creationDate * 1000), t('coreshop_date_time_format'))
                            }
                        ]
                    });
                }
            }

            items.push({
                xtype: 'tabpanel',
                items: [
                    this.getAddressPanelForAddress(this.order.address.shipping, t('coreshop_address_shipping'), 'shipping'),
                    this.getAddressPanelForAddress(this.order.address.billing, t('coreshop_address_billing'), 'billing')
                ]
            });

            var guestStr = this.order.customer.isGuest ? ' –  ' + t('coreshop_order_is_guest') : '';
            this.customerInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_customer') + ': ' + (this.order.customer ? this.order.customer.firstname + ' (' + this.order.customer.o_id + ')' : t('unknown')) + guestStr,
                margin : '0 0 20 0',
                border : true,
                flex : 6,
                iconCls : 'coreshop_icon_customer',
                tools : [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler : function () {
                            if(this.order.customer) {
                                pimcore.helpers.openObject(this.order.customer.o_id);
                            }
                        }.bind(this)
                    }
                ],
                items : items
            });
        }

        return this.customerInfo;
    },

    getAddressPanelForAddress : function (address, title, type) {
        var country = pimcore.globalmanager.get("coreshop_countries").getById(address.country);

        var panel = {
            xtype: 'panel',
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    {
                        iconCls : 'pimcore_icon_edit',
                        text : t('edit'),
                        scale : 'small',
                        handler : function () {
                            Ext.Ajax.request({
                                url: '/admin/CoreShop/order/get-address-fields',
                                params: {
                                    id: this.order.o_id,
                                    type : type
                                },
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);

                                    var addressWindow = new pimcore.plugin.coreshop.orders.address(res.data, res.layout, this.order.o_id, type, title, function (success) {
                                        addressWindow.close();
                                        if (success) {
                                            this.reload();
                                        }
                                    }.bind(this)).show();
                                }.bind(this)
                            });
                        }.bind(this)
                    }, '->',
                    {
                        iconCls: 'coreshop_icon_open',
                        text: t('open'),
                        handler : function () {
                            pimcore.helpers.openObject(address.o_id);
                        }.bind(this)
                    }
                ]
            }],
            title: title,
            layout: {
                type : 'hbox',
                align : 'stretch'
            },
            height : 220,
            items: [
                {
                    xtype: 'panel',
                    bodyPadding : 5,
                    html : address.formatted,
                    flex : 1
                }
            ]
        };

        if(pimcore.settings.google_maps_api_key) {
            panel.items.push({
                xtype: 'panel',
                html : '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap'
                + '&center=' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + country.get("name")
                + '&markers=color:blue|' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + country.get("name")
                + '&key=' + pimcore.settings.google_maps_api_key
                + '" />',
                flex : 1,
                bodyPadding : 5
            });
        }

        return panel;
    },

    getCarrierAndPaymentDetails : function() {
        if (!this.carrierPaymentDetails) {
            var items = [

            ];

            items.push({
                xtype : 'panel',
                layout : 'hbox',
                items : [
                    {
                        xtype : 'panel',
                        flex : 1,
                        items : [
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_paymentProvider'),
                                html : '<span style="font-weight:bold;">'+t('coreshop_paymentProvider')+': </span>' + this.order.shippingPayment.payment
                            },
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_currency'),
                                html : '<span style="font-weight:bold;">'+t('coreshop_currency')+': </span>' + this.order.currency.name
                            }
                        ]
                    },
                    {
                        xtype : 'panel',
                        flex : 1,
                        items : [
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_carrier'),
                                html : '<span style="font-weight:bold;">'+t('coreshop_carrier')+': </span>' + this.order.shippingPayment.carrier
                            },
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_carrier_price'),
                                html : '<span style="font-weight:bold;">'+t('coreshop_carrier_price')+': </span>' + coreshop.util.format.currency(this.order.currency.symbol, this.order.shippingPayment.cost)
                            },
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_carrier_shippingMethod_weight'),
                                html: '<span style="font-weight:bold;">'+t('coreshop_carrier_shippingMethod_weight')+': </span>' + (this.order.shippingPayment.weight ? this.order.shippingPayment.weight : 0)
                            }
                        ]
                    }
                ]
            });

            this.carrierPaymentDetails = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_carrier') + '/' + t('coreshop_paymentProvider'),
                margin : '0 20 20 0',
                border : true,
                flex : 6,
                iconCls : 'coreshop_icon_carrier',
                items : items
            });
        }

        return this.carrierPaymentDetails;
    },

    getShipmentDetails : function () {
        if (!this.shippingInfo) {
            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners : {
                    edit : function (editor, context, eOpts) {
                        var trackingCode = context.record.get('trackingCode');

                        Ext.Ajax.request({
                            url: '/admin/CoreShop/order-shipment/change-tracking-code',
                            params: {
                                shipmentId : context.record.get("o_id"),
                                trackingCode : trackingCode
                            },
                            success: function (response) {
                                context.record.commit();

                            }.bind(this)
                        });
                    }.bind(this)
                }
            });

            this.shipmentsStore = new Ext.data.JsonStore({
                data : this.order.shipments
            });

            this.shippingInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_shipments'),
                border : true,
                margin : '0 20 20 0',
                iconCls : 'coreshop_icon_orders_shipment',
                items : [
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-detail-grid',
                        store :  this.shipmentsStore,
                        plugins: [
                            cellEditing
                        ],
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'carrier',
                                text : t('coreshop_carrier'),
                                flex : 3,
                                renderer : function (value) {
                                    var store = pimcore.globalmanager.get('coreshop_carriers');
                                    var carrier = store.getById(value);

                                    if (carrier) {
                                        return carrier.get("name");
                                    }

                                    return value;
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'weight',
                                text : t('coreshop_carrier_shippingMethod_weight')
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'trackingCode',
                                text : t('coreshop_carrier_tracking_code'),
                                flex : 2,
                                field: {
                                    xtype: 'textfield'
                                }
                            },
                            {
                                menuDisabled: true,
                                sortable: false,
                                xtype: 'actioncolumn',
                                width: 32,
                                items: [{
                                    iconCls: 'pimcore_icon_edit',
                                    tooltip: t('Edit'),
                                    handler: function (grid, rowIndex, colIndex) {
                                        cellEditing.startEditByPosition({
                                            row: rowIndex,
                                            column : colIndex - 1
                                        });
                                    }.bind(this)
                                }]
                            },
                            {
                                xtype: 'widgetcolumn',
                                flex : 2,
                                widget: {
                                    xtype: 'button',
                                    margin : '5 0 5 0',
                                    padding: '3 4 3 4',
                                    _btnText: '',
                                    tooltip : t('open'),
                                    defaultBindProperty: null,
                                    handler: function(widgetColumn) {
                                        var record = widgetColumn.getWidgetRecord();
                                        pimcore.helpers.openObject(record.data.o_id, 'object');
                                    },
                                    listeners: {
                                        beforerender: function(widgetColumn){
                                            var record = widgetColumn.getWidgetRecord();
                                            widgetColumn.setText( Ext.String.format(t('coreshop_shipment_order'), record.data.shipmentNumber) );
                                        }
                                    }
                                }
                            }
                        ]
                    }
                ],
                tools : [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler : function () {
                            this.createShipment();
                        }.bind(this)
                    }
                ]
            });
        }

        return this.shippingInfo;
    },

    getInvoiceDetails : function () {
        if (!this.invoiceDetails) {
            this.invoicesStore = new Ext.data.JsonStore({
                data : this.order.invoices
            });

            this.invoiceDetails = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_invoices'),
                border : true,
                margin : '0 20 20 0',
                iconCls : 'coreshop_icon_orders_invoice',
                items : [
                    {
                        xtype : 'grid',
                        margin: '5 0 15 0',
                        cls : 'coreshop-detail-grid',
                        store : this.invoicesStore,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'invoiceNumber',
                                text : t('coreshop_invoice_number'),
                                flex : 1
                            },
                            {
                                xtype : 'gridcolumn',
                                flex : 2,
                                dataIndex : 'invoiceDate',
                                text : t('coreshop_invoice_date'),
                                renderer : function (val) {
                                    if (val) {
                                        return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                                    }

                                    return '';
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'totalWithoutTax',
                                text : t('coreshop_total_without_tax'),
                                flex : 2,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'total',
                                text : t('coreshop_total'),
                                flex : 2,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype: 'widgetcolumn',
                                flex : 2,
                                widget: {
                                    xtype: 'button',
                                    margin : '5 0 5 0',
                                    padding: '3 4 3 4',
                                    _btnText: '',
                                    defaultBindProperty: null,
                                    handler: function(widgetColumn) {
                                        var record = widgetColumn.getWidgetRecord();
                                        pimcore.helpers.openObject(record.data.o_id, 'object');
                                    },
                                    listeners: {
                                        beforerender: function(widgetColumn){
                                            var record = widgetColumn.getWidgetRecord();
                                            widgetColumn.setText(Ext.String.format(t('coreshop_invoice_order'), record.data.invoiceNumber));
                                        }
                                    }
                                }
                            }
                        ]
                    }
                ],
                tools : [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler : function () {
                            this.createInvoice();
                        }.bind(this)
                    }
                ]
            });
        }

        return this.invoiceDetails;
    },

    getMailDetails : function () {
        if (!this.mailCorrespondence) {
            this.mailCorrespondenceStore = new Ext.data.JsonStore({
                data : this.order.mailCorrespondence
            });

            this.mailCorrespondence = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_mail_correspondence'),
                border : true,
                scrollable: 'y',
                maxHeight:360,
                margin : '0 20 20 0',
                iconCls : 'coreshop_icon_mail',
                items : [
                    {
                        xtype : 'grid',
                        margin: '5 0 15 0',
                        cls : 'coreshop-detail-grid',
                        store : this.mailCorrespondenceStore,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'date',
                                text : t('coreshop_date'),
                                renderer : function (val) {
                                    if (val) {
                                        return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                                    }
                                    return '';
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'subject',
                                text : t('coreshop_mail_correspondence_subject'),
                                flex : 2
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'recipient',
                                text : t('coreshop_mail_correspondence_recipient'),
                                flex : 2
                            },
                            {
                                xtype : 'gridcolumn',
                                text : t('coreshop_messaging_message_read'),
                                width : 100,
                                renderer : function(value, metaData, rec) {
                                    if(Ext.isDefined(rec.get('read'))) {
                                        return rec.get('read') ? t('yes') : t('no');
                                    }

                                    return '';
                                }
                            },
                            {
                                xtype: 'actioncolumn',
                                sortable: false,
                                width: 50,
                                dataIndex: 'emailLogExistsHtml',
                                header: t('email_log_html'),
                                items: [{
                                    tooltip: t('email_log_show_html_email'),
                                    handler: function(grid, rowIndex){
                                        var rec = grid.getStore().getAt(rowIndex),
                                            iFrameSettings = { width : 700, height : 500},
                                            iFrame = new Ext.Window(
                                                {
                                                    title: t('email_log_iframe_title_html'),
                                                    width: iFrameSettings.width,
                                                    height: iFrameSettings.height,
                                                    layout: 'fit',
                                                    items : [
                                                        {
                                                            xtype : 'box',
                                                            autoEl: {
                                                                tag: 'iframe',
                                                                src: '/admin/email/show-email-log/?id=' + rec.get('email-log') + '&type=html'}
                                                        }
                                                    ]
                                                }
                                            );
                                        iFrame.show();
                                    },
                                    getClass: function(v, meta, rec) {
                                        if(!Ext.isDefined(rec.get('email-log')) || rec.get('email-log') === null) {
                                            return 'pimcore_hidden';
                                        }

                                        return 'pimcore_icon_newsletter';
                                    }
                                }]
                            },
                            {
                                menuDisabled: true,
                                sortable: false,
                                xtype: 'actioncolumn',
                                width: 50,
                                items: [{
                                    iconCls: 'pimcore_icon_open',
                                    tooltip: t('open'),
                                    handler : function (grid, rowIndex) {
                                        var record = grid.getStore().getAt(rowIndex);
                                        pimcore.helpers.openDocument(record.get('emailId'), 'email');
                                    }
                                }]
                            },
                            {
                                xtype: 'actioncolumn',
                                width: 50,
                                sortable: false,
                                items: [{
                                    tooltip: t('open'),
                                    handler: function(grid, rowIndex){
                                        var rec = grid.getStore().getAt(rowIndex);
                                        var threadId = rec.get("threadId");

                                        if(threadId) {
                                            coreshop.helpers.openMessagingThread(threadId);
                                        }

                                    },
                                    getClass: function(v, meta, rec) {
                                        if(!Ext.isDefined(rec.get('threadId')) || rec.get('threadId') === null) {
                                            return 'pimcore_hidden';
                                        }

                                        return 'coreshop_icon_messaging_thread';
                                    }
                                }]
                            }
                        ]
                    }
                ]
            });
        }

        return this.mailCorrespondence;
    },

    updatePaymentInfoAlert : function () {
        if (this.paymentInfoAlert) {
            if (this.order.totalPayed < this.order.total || this.order.totalPayed > this.order.total) {
                this.paymentInfoAlert.update(t('coreshop_order_payment_paid_warning').format(coreshop.util.format.currency(this.order.currency.symbol, this.order.totalPayed), coreshop.util.format.currency(this.order.currency.symbol, this.order.total)));
                this.paymentInfoAlert.show();
            } else {
                this.paymentInfoAlert.update('');
                this.paymentInfoAlert.hide();
            }
        }
    },

    getPaymentDetails : function () {
        if (!this.paymentInfo) {
            this.paymentsStore = new Ext.data.JsonStore({
                data : this.order.payments
            });

            this.paymentInfoAlert = Ext.create('Ext.panel.Panel', {
                xtype : 'panel',
                cls : 'x-coreshop-alert',
                bodyPadding : 5,
                hidden : true
            });
            this.updatePaymentInfoAlert();

            var items = [
                this.paymentInfoAlert
            ];

            items.push({
                xtype : 'grid',
                margin: '0 0 15 0',
                cls : 'coreshop-detail-grid',
                store :  this.paymentsStore,
                columns : [
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'datePayment',
                        text : t('date'),
                        flex : 3,
                        renderer : function (val) {
                            if (val) {
                                return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                            }

                            return '';
                        }
                    },
                    {
                        xtype : 'gridcolumn',
                        flex : 3,
                        dataIndex : 'provider',
                        text : t('coreshop_paymentProvider')
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'transactionIdentifier',
                        text : t('coreshop_transactionNumber'),
                        flex : 1,
                        align : 'right'
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'amount',
                        text : t('coreshop_amount'),
                        flex : 1,
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 32,
                        items: [{
                            iconCls: 'pimcore_icon_object',
                            tooltip: t('coreshop_show_transaction_notes'),
                            handler : function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);
                                this.showPaymentTransactions(record.get('transactionNotes'));
                            }.bind(this)
                        }]
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 32,
                        items: [{
                            iconCls: 'pimcore_icon_open',
                            tooltip: t('open'),
                            handler : function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);
                                pimcore.helpers.openObject(record.get('id'));
                            }
                        }]
                    }
                ]
            });

            this.paymentInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_payments'),
                border : true,
                margin : '0 20 20 0',
                iconCls : 'coreshop_icon_payment',
                tools : [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler : function () {
                            pimcore.plugin.coreshop.orders.createPayment.showWindow(this.order.o_id, this.order, function (result) {
                                if (result.success) {
                                    this.reload();
                                }
                            }.bind(this));
                        }.bind(this)
                    }
                ],
                items : items
            });
        }

        return this.paymentInfo;
    },

    getMessagesInfo : function () {
        if (!this.messagesInfo) {
            this.messagesInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_messaging_messages'),
                border : true,
                margin : '0 0 20 0',
                iconCls : 'coreshop_icon_messaging',
                items : [
                    {
                        xtype : 'form',
                        bodyStyle:'padding:20px 5px 20px 5px;',
                        border: false,
                        autoScroll: true,
                        forceLayout: true,
                        fieldDefaults: {
                            labelWidth: 150
                        },
                        buttons: [
                            {
                                text: t('coreshop_message_send'),
                                handler: function (btn) {
                                    var formObj = btn.up('form');
                                    var form = formObj.getForm();

                                    if (form.isValid()) {
                                        var formValues = form.getFieldValues();

                                        formValues['o_id'] = this.order.o_id;

                                        Ext.Ajax.request({
                                            url : '/admin/CoreShop/order/send-message',
                                            method : 'post',
                                            params : formValues,
                                            callback: function (request, success, response) {
                                                try {
                                                    response = Ext.decode(response.responseText);

                                                    if (response.success) {
                                                        formObj.down('textarea').setValue('');

                                                        pimcore.helpers.showNotification(t('success'), t('coreshop_message_send_success'), 'success');
                                                    } else {
                                                        Ext.Msg.alert(t('error'), response.message);
                                                    }
                                                }
                                                catch (e) {
                                                    Ext.Msg.alert(t('error'), e);
                                                }
                                            }
                                        });
                                    }
                                }.bind(this),
                                iconCls: 'pimcore_icon_apply'
                            }
                        ],
                        items : [
                            {
                                xtype: 'textarea',
                                name: 'message',
                                style: "font-family: 'Courier New', Courier, monospace;",
                                width : '100%',
                                height : '100%'
                            }
                        ]
                    }
                ]
            });
        }

        return this.messagesInfo;
    },

    getPluginInfo : function () {
        var pluginInfo = coreshop.plugin.broker.fireEvent('orderDetail', this);

        if (pluginInfo.length > 0) {

            return {
                xtype: 'container',
                layout: 'hbox',
                margin: '0 0 20 0',
                border: 0,
                style: {
                    border: 0
                },
                items: pluginInfo
            };
        }

        return null;
    },

    getPaymentPluginInfo : function () {
        var pluginInfo = coreshop.plugin.broker.fireEvent('orderDetailPayment' + this.order.paymentProvider.ucfirst(), this);

        if (pluginInfo) {
            return {
                xtype: 'container',
                layout: 'hbox',
                margin: '0 0 20 0',
                border: 0,
                style: {
                    border: 0
                },
                items: pluginInfo
            };
        }

        return null;
    },

    getVisitorInfo : function() {
        if(this.order.visitor) {
            if (!this.visitorInfo) {
                var visitor = this.order.visitor;

                this.visitorInfo = Ext.create('Ext.panel.Panel', {
                    title : t('coreshop_visitor'),
                    border : true,
                    margin : '0 20 20 0',
                    iconCls : 'coreshop_icon_visitor',
                    items : [
                        {
                            xtype: 'panel',
                            bodyPadding : 5,
                            html :
                            ('<strong>' + t('coreshop_visitor_ip') + '</strong>: ' + coreshop.helpers.long2ip(visitor.ip))  + '<br/>' +
                            ('<strong>' + t('coreshop_visitor_referrer') + '</strong>: ' + visitor.referrer) + '<br/>' +
                            ('<strong>' + t('coreshop_visitor_date')+ '</strong>: ' + Ext.Date.format(new Date(visitor.creationDate * 1000), t('coreshop_date_time_format'))) + '<br/>',
                            flex : 1
                        }
                    ]
                });
            }

            return this.visitorInfo;
        }

        return false;
    },

    getDetailInfo : function () {
        if (!this.detailsInfo) {
            this.detailsStore = new Ext.data.JsonStore({
                data : this.order.details
            });

            this.summaryStore = new Ext.data.JsonStore({
                data : this.order.summary
            });

            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners : {
                    edit : function (editor, context, eOpts) {
                        if (context.originalValue != context.value) {
                            Ext.Ajax.request({
                                url: '/admin/CoreShop/order/change-order-item',
                                params: {
                                    id: this.order.o_id,
                                    orderItemId : context.record.get('o_id'),
                                    amount : context.record.get('amount'),
                                    price : context.record.get('price_without_tax')
                                },
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);

                                    if (res.success) {
                                        context.record.commit();

                                        //this.reload();

                                        this.detailsStore.loadData(res.details);
                                        this.summaryStore.loadData(res.summary);

                                        this.order.total = res.total;

                                        this.updatePaymentInfoAlert();
                                    } else {
                                        pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                                    }

                                }.bind(this)
                            });
                        }
                    }.bind(this)
                }
            });

            var plugins = [];
            var actions = [
                {
                    iconCls: 'pimcore_icon_open',
                    tooltip : t('open'),
                    handler : function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        pimcore.helpers.openObject(record.get('o_id'));
                    }
                }
            ];

            if(this.order.editable) {
                plugins.push(cellEditing);
                actions.push({
                    iconCls: 'pimcore_icon_edit',
                    tooltip: t('edit'),
                    handler: function (grid, rowIndex, colIndex) {
                        cellEditing.startEditByPosition({
                            row: rowIndex,
                            column : 4
                        });
                    }.bind(this)
                });
            }

            var itemsGrid = {
                xtype : 'grid',
                margin: '0 0 15 0',
                cls : 'coreshop-detail-grid',
                store :  this.detailsStore,
                plugins: plugins,
                columns : [
                    {
                        xtype : 'gridcolumn',
                        flex : 1,
                        dataIndex : 'product_name',
                        text : t('coreshop_product')
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'wholesale_price',
                        text : t('coreshop_wholesale_price'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'price_without_tax',
                        text : t('coreshop_price_without_tax'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol),
                        field : {
                            xtype: 'numberfield',
                            decimalPrecision : 4
                        }
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'price',
                        text : t('coreshop_price_with_tax'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'amount',
                        text : t('coreshop_amount'),
                        width : 150,
                        align : 'right',
                        field : {
                            xtype: 'numberfield',
                            decimalPrecision : 0
                        }
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'total',
                        text : t('coreshop_total'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 50,
                        items: actions
                    }
                ]
            };

            var summaryGrid = {
                xtype : 'grid',
                margin: '0 0 15 0',
                cls : 'coreshop-detail-grid',
                store :  this.summaryStore,
                hideHeaders : true,
                columns : [
                    {
                        xtype : 'gridcolumn',
                        flex : 1,
                        align: 'right',
                        dataIndex : 'key',
                        renderer : function (value, metaData, record) {
                            if(record.get("text")) {
                                return '<span style="font-weight:bold">' + record.get("text") + '</span>';
                            }

                            return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                        }
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'value',
                        width : 150,
                        align : 'right',
                        renderer : function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.order.currency.symbol, value) + '</span>';
                        }.bind(this)
                    }
                ]
            };

            var detailItems = [itemsGrid, summaryGrid];

            if (this.order.priceRule) {

                var priceRuleStore = new Ext.data.JsonStore({
                    data : this.order.priceRule
                });

                var priceRuleItem =  {
                    xtype : 'grid',
                    margin: '0 0 15 0',
                    cls : 'coreshop-detail-grid',
                    store :  priceRuleStore,
                    hideHeaders : true,
                    title : t('coreshop_pricerules'),
                    columns : [
                        {
                            xtype : 'gridcolumn',
                            flex : 1,
                            align: 'right',
                            dataIndex : 'name'
                        },
                        {
                            xtype : 'gridcolumn',
                            dataIndex : 'discount',
                            width : 150,
                            align : 'right',
                            renderer : function (value, metaData, record) {
                                return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.order.currency.symbol, value) + '</span>';
                            }.bind(this)
                        }
                    ]
                };

                detailItems.splice(1, 0, priceRuleItem);
            }

            this.detailsInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_products'),
                border : true,
                margin : '0 0 20 0',
                iconCls : 'coreshop_icon_product',
                items : detailItems
            });
        }

        return this.detailsInfo;
    },

    setWorkflowInfo : function() {

        var buttons = [],
            toolbar;

        //add reload function for worfklow manager!
        this.objectData.reload = this.reload.bind(this);

        if( this.objectData.workflowManagement) {
            this.objectData.data.workflowManagement = this.objectData.workflowManagement;
        }

        pimcore.elementservice.integrateWorkflowManagement('object', this.order.o_id, this.objectData, buttons);

        toolbar = new Ext.Toolbar({
            border: false,
            items: buttons,
            overflowHandler: 'scroller'
        });

        this.orderInfo.insert(0, toolbar);
    },

    createInvoice : function() {
        new pimcore.plugin.coreshop.orders.invoice(this.order, function() {
            this.reload();
        }.bind(this));
    },

    createShipment : function() {
        new pimcore.plugin.coreshop.orders.shipment(this.order, function() {
            this.reload();
        }.bind(this));
    },

    showPaymentTransactions: function(paymentTransactions) {
        if (paymentTransactions.length === 0) {
            Ext.Msg.alert(t('error'), t('coreshop_no_payment_transactions'));
            return false;
        }

        var transactionStore = new Ext.data.JsonStore({
            data : paymentTransactions
        });

        var itemsGrid = {
            xtype : 'grid',
            margin: '0 0 15 0',
            cls : 'coreshop-detail-grid',
            store : transactionStore,
            columns : [
                {
                    xtype : 'gridcolumn',
                    flex : 1,
                    dataIndex : 'date',
                    text : t('date'),
                    renderer : function (val) {
                        if (val) {
                            return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                        }
                        return '';
                    }
                },
                {
                    xtype : 'gridcolumn',
                    dataIndex : 'title',
                    text : t('coreshop_transaction_id'),
                    flex: 1
                },
                {
                    xtype : 'gridcolumn',
                    dataIndex : 'description',
                    text : t('description'),
                    flex: 1
                }
            ]
        };

        var window = new Ext.window.Window({
            width : 600,
            height : 300,
            resizeable : false,
            layout : 'fit',
            items : [{
                xtype : 'form',
                bodyStyle:'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: t('close'),
                        handler: function (btn) {
                            window.destroy();
                            window.close();
                        },

                        iconCls: 'pimcore_icon_accept'
                    }
                ],
                items : itemsGrid
            }]
        });

        window.show();
        return window;
    }
});
