/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.detail');
coreshop.order.sale.detail = Class.create({

    order: null,
    objectData: null,
    layoutId: null,
    type: 'sale',
    iconCls: '',

    borderStyle: {
        borderStyle: 'solid',
        borderColor: '#ccc',
        borderRadius: '5px',
        borderWidth: '1px'
    },

    initialize: function (sale) {
        this.sale = sale;
        this.sale = sale;
        this.layoutId = 'coreshop_' + this.type + '_' + this.sale.o_id;
        this.iconCls = 'coreshop_icon_' + this.type;
        this.getLayout();
        this.getObjectInfo();
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    reload: function () {
        this.layout.destroy();

        coreshop.order.helper.openSale(this.sale.o_id, this.type);
    },

    getObjectInfo: function () {

        Ext.Ajax.request({
            url: '/admin/object/get',
            params: {id: this.sale.o_id},
            success: function (response) {
                try {
                    this.objectData = Ext.decode(response.responseText);
                    this.setWorkflowInfo();

                } catch (e) {
                }
            }.bind(this)
        });
    },

    getTopButtons: function () {
        return [];
    },

    getLayout: function () {
        if (!this.layout) {

            var buttons = [{
                iconCls: 'pimcore_icon_reload',
                text: t('reload'),
                handler: function () {
                    this.reload();
                }.bind(this)
            }];

            buttons = buttons.concat(this.getTopButtons());

            // create new panel
            this.layout = new Ext.panel.Panel({
                id: this.layoutId,
                title: t('coreshop_' + this.type) + ': ' + this.sale.saleNumber,
                iconCls: this.iconCls,
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

    getLeftItems: function () {
        return [
            this.getSaleInfo(),
            this.getMailDetails()
        ];
    },

    getRightItems: function () {
        var items = [this.getCustomerInfo()];
        if(typeof coreshop.order.sale.detail.additionalData === 'function') {
            var additionalData = new coreshop.order.sale.detail.additionalData(this.sale, this.sale.additionalData);
            if(typeof additionalData.getItems === 'function') {
                var additionalItems = additionalData.getItems();
                //custom additional data
                if(Ext.isArray(additionalItems)) {
                    var container = Ext.create('Ext.panel.Panel', {
                        title: t('coreshop_order_additional_data'),
                        margin: '0 0 20 0',
                        border: true,
                        flex: 6,
                        iconCls: 'coreshop_icon_additional_data',
                        items: additionalItems
                    });
                    items.push(container)
                }
            }
        }
        return items;
    },

    getFullItems: function () {
        var pluginPanel = this.getPluginInfo();
        var items = [];

        if (pluginPanel) {
            items.push(pluginPanel);
        }

        return items;
    },

    getPanel: function () {
        var defaults = {
            style: this.borderStyle,
            cls: 'coreshop-panel',
            bodyPadding: 5
        };

        var leftItems = this.getLeftItems();
        var rightItems = this.getRightItems();

        var contentItems = [
            {
                xtype: 'container',
                border: 0,
                style: {
                    border: 0
                },
                flex: 7,
                defaults: defaults,
                items: leftItems
            },
            {
                xtype: 'container',
                border: 0,
                style: {
                    border: 0
                },
                flex: 5,
                defaults: defaults,
                items: rightItems
            }
        ];

        var items = [
            this.getHeader(),
            {
                xtype: 'container',
                layout: 'hbox',
                margin: '0 0 20 0',
                border: 0,
                style: {
                    border: 0
                },
                items: contentItems
            }
        ];

        items = items.concat(this.getFullItems());
        items.push(this.getDetailInfo());

        this.panel = Ext.create('Ext.container.Container', {
            border: false,
            items: items,
            padding: 20,
            region: 'center',
            defaults: defaults
        });

        return this.panel;
    },

    getHeader: function () {
        if (!this.headerPanel) {
            var items = [
                {
                    xtype: 'panel',
                    html: t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(this.sale.saleDate * 1000), t('coreshop_date_time_format')) + '</span>',
                    bodyPadding: 20,
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_sale_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(this.sale.currency.symbol, this.sale.totalGross) + '</span>',
                    bodyPadding: 20,
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + this.sale.items.length + '</span>',
                    bodyPadding: 20,
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_store') + '<br/><span class="coreshop_order_big">' + this.sale.store.name + '</span>',
                    bodyPadding: 20,
                    flex: 1
                }
            ];

            this.headerPanel = Ext.create('Ext.panel.Panel', {
                layout: 'hbox',
                margin: '0 0 20 0',
                items: items
            });
        }

        return this.headerPanel;
    },

    getSaleInfo: function () {
        if (!this.saleInfo) {
            this.saleInfo = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_' + this.type) + ': ' + this.sale.saleNumber + ' (' + this.sale.o_id + ')',
                margin: '0 20 20 0',
                border: true,
                flex: 8,
                iconCls: this.iconCls,
                tools: [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler: function () {
                            pimcore.helpers.openObject(this.sale.o_id);
                        }.bind(this)
                    }
                ]
            });
        }

        return this.saleInfo;
    },

    getCustomerInfo: function () {
        if (!this.customerInfo) {
            var items = [];

            if (this.sale.customer) {
                if (!this.sale.customer.isGuest) {

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
                                text: this.sale.customer.email
                            },
                            {
                                xtype: 'label',
                                style: 'font-weight:bold;display:block',
                                text: t('coreshop_customer_created')
                            },
                            {
                                xtype: 'label',
                                style: 'display:block',
                                text: Ext.Date.format(new Date(this.sale.customer.o_creationDate * 1000), t('coreshop_date_time_format'))
                            }
                        ]
                    });
                }
            }

            if (this.sale.comment) {
                items.push({
                    xtype: 'panel',
                    bodyPadding: 10,
                    margin: '0 0 10px 0',
                    style: this.borderStyle,
                    items: [
                        {
                            xtype: 'label',
                            style: 'font-weight:bold;display:block',
                            text: t('coreshop_comment')
                        },
                        {
                            xtype: 'label',
                            style: 'display:block',
                            html: Ext.util.Format.nl2br(this.sale.comment)
                        }
                    ]
                });
            }

            items.push({
                xtype: 'tabpanel',
                items: [
                    this.getAddressPanelForAddress(this.sale.address.shipping, t('coreshop_address_shipping'), 'shipping'),
                    this.getAddressPanelForAddress(this.sale.address.billing, t('coreshop_address_invoice'), 'invoice')
                ]
            });

            var guestStr = this.sale.customer.isGuest ? ' –  ' + t('coreshop_is_guest') : '';
            this.customerInfo = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_customer') + ': ' + (this.sale.customer ? this.sale.customer.firstname + ' (' + this.sale.customer.o_id + ')' : t('unknown')) + guestStr,
                margin: '0 0 20 0',
                border: true,
                flex: 6,
                iconCls: 'coreshop_icon_customer',
                tools: [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler: function () {
                            if (this.sale.customer) {
                                pimcore.helpers.openObject(this.sale.customer.o_id);
                            }
                        }.bind(this)
                    }
                ],
                items: items
            });
        }

        return this.customerInfo;
    },

    getAddressPanelForAddress: function (address, title, type) {
        var country = pimcore.globalmanager.get("coreshop_countries").getById(address.country);

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
                        }.bind(this)
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
    },

    getMailDetails: function () {
        if (!this.mailCorrespondence) {
            this.mailCorrespondenceStore = new Ext.data.JsonStore({
                data: this.sale.mailCorrespondence
            });

            this.mailCorrespondence = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_mail_correspondence'),
                border: true,
                scrollable: 'y',
                maxHeight: 360,
                margin: '0 20 20 0',
                iconCls: 'coreshop_icon_mail',
                items: [
                    {
                        xtype: 'grid',
                        margin: '5 0 15 0',
                        cls: 'coreshop-detail-grid',
                        store: this.mailCorrespondenceStore,
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                flex: 1,
                                dataIndex: 'date',
                                text: t('coreshop_date'),
                                renderer: function (val) {
                                    if (val) {
                                        return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                                    }
                                    return '';
                                }
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'subject',
                                text: t('coreshop_mail_correspondence_subject'),
                                flex: 2
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'recipient',
                                text: t('coreshop_mail_correspondence_recipient'),
                                flex: 2
                            },
                            {
                                xtype: 'gridcolumn',
                                text: t('coreshop_messaging_message_read'),
                                width: 100,
                                renderer: function (value, metaData, rec) {
                                    if (Ext.isDefined(rec.get('read'))) {
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
                                    handler: function (grid, rowIndex) {
                                        var rec = grid.getStore().getAt(rowIndex),
                                            iFrameSettings = {width: 700, height: 500},
                                            iFrame = new Ext.Window(
                                                {
                                                    title: t('email_log_iframe_title_html'),
                                                    width: iFrameSettings.width,
                                                    height: iFrameSettings.height,
                                                    layout: 'fit',
                                                    items: [
                                                        {
                                                            xtype: 'box',
                                                            autoEl: {
                                                                tag: 'iframe',
                                                                src: '/admin/email/show-email-log?id=' + rec.get('email-log') + '&type=html'
                                                            }
                                                        }
                                                    ]
                                                }
                                            );
                                        iFrame.show();
                                    },
                                    getClass: function (v, meta, rec) {
                                        if (!Ext.isDefined(rec.get('email-log')) || rec.get('email-log') === null) {
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
                                    handler: function (grid, rowIndex) {
                                        var record = grid.getStore().getAt(rowIndex);
                                        pimcore.helpers.openDocument(record.get('document'), 'email');
                                    }
                                }]
                            },
                            {
                                xtype: 'actioncolumn',
                                width: 50,
                                sortable: false,
                                items: [{
                                    tooltip: t('open'),
                                    handler: function (grid, rowIndex) {
                                        var rec = grid.getStore().getAt(rowIndex);
                                        var threadId = rec.get('threadId');

                                        if (threadId) {
                                            coreshop.helpers.openMessagingThread(threadId);
                                        }

                                    },
                                    getClass: function (v, meta, rec) {
                                        if (!Ext.isDefined(rec.get('threadId')) || rec.get('threadId') === null) {
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

    getPluginInfo: function () {
        /*var pluginInfo = coreshop.plugin.broker.fireEvent(this.type + 'Detail', this);

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
         }*/

        return null;
    },

    getDetailInfo: function () {
        if (!this.detailsInfo) {
            this.detailsStore = new Ext.data.JsonStore({
                data: this.sale.details
            });

            this.summaryStore = new Ext.data.JsonStore({
                data: this.sale.summary
            });

            /*var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
             listeners: {
             edit: function (editor, context, eOpts) {
             if (context.originalValue !== context.value) {
             Ext.Ajax.request({
             url: '/admin/coreshop/' + this.type + '/change-item',
             params: {
             id: this.sale.o_id,
             orderItemId: context.record.get('o_id'),
             amount: context.record.get('amount'),
             price: context.record.get('price_without_tax')
             },
             success: function (response) {
             var res = Ext.decode(response.responseText);

             if (res.success) {
             context.record.commit();

             //this.reload();

             this.detailsStore.loadData(res.details);
             this.summaryStore.loadData(res.summary);

             this.sale.totalGross = res.totalGross;

             this.updatePaymentInfoAlert();
             } else {
             pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
             }

             }.bind(this)
             });
             }
             }.bind(this)
             }
             });*/

            var plugins = [];
            var actions = [
                {
                    iconCls: 'pimcore_icon_open',
                    tooltip: t('open'),
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        pimcore.helpers.openObject(record.get('o_id'));
                    }
                }
            ];

            if (this.sale.editable) {
                //plugins.push(cellEditing);
                /*actions.push({
                 iconCls: 'pimcore_icon_edit',
                 tooltip: t('edit'),
                 handler: function (grid, rowIndex, colIndex) {
                 cellEditing.startEditByPosition({
                 row: rowIndex,
                 column: 4
                 });
                 }.bind(this)
                 });*/
            }

            var itemsGrid = {
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.detailsStore,
                plugins: plugins,
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'product_name',
                        text: t('coreshop_product')
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'wholesale_price',
                        text: t('coreshop_wholesale_price'),
                        width: 150,
                        align: 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'price_without_tax',
                        text: t('coreshop_price_without_tax'),
                        width: 150,
                        align: 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol),
                        field: {
                            xtype: 'numberfield',
                            decimalPrecision: 4
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'price',
                        text: t('coreshop_price_with_tax'),
                        width: 150,
                        align: 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'amount',
                        text: t('coreshop_quantity'),
                        width: 150,
                        align: 'right',
                        field: {
                            xtype: 'numberfield',
                            decimalPrecision: 0
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'total',
                        text: t('coreshop_total'),
                        width: 150,
                        align: 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
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
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.summaryStore,
                hideHeaders: true,
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        align: 'right',
                        dataIndex: 'key',
                        renderer: function (value, metaData, record) {
                            if (record.get("text")) {
                                return '<span style="font-weight:bold">' + record.get("text") + '</span>';
                            }

                            return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'value',
                        width: 150,
                        align: 'right',
                        renderer: function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.sale.currency.symbol, value) + '</span>';
                        }.bind(this)
                    }
                ]
            };

            var detailItems = [itemsGrid, summaryGrid];

            if (this.sale.priceRule) {

                var priceRuleStore = new Ext.data.JsonStore({
                    data: this.sale.priceRule
                });

                var priceRuleItem = {
                    xtype: 'grid',
                    margin: '0 0 15 0',
                    cls: 'coreshop-detail-grid',
                    store: priceRuleStore,
                    hideHeaders: true,
                    title: t('coreshop_pricerules'),
                    columns: [
                        {
                            xtype: 'gridcolumn',
                            flex: 1,
                            align: 'right',
                            dataIndex: 'name'
                        },
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'discount',
                            width: 150,
                            align: 'right',
                            renderer: function (value, metaData, record) {
                                return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.sale.currency.symbol, value) + '</span>';
                            }.bind(this)
                        }
                    ]
                };

                detailItems.splice(1, 0, priceRuleItem);
            }

            this.detailsInfo = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_products'),
                border: true,
                margin: '0 0 20 0',
                iconCls: 'coreshop_icon_product',
                items: detailItems
            });
        }

        return this.detailsInfo;
    }
});
