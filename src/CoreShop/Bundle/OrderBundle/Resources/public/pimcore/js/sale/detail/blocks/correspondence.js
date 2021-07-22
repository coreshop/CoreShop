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

pimcore.registerNS('coreshop.order.sale.detail.blocks.correspondence');
coreshop.order.sale.detail.blocks.correspondence = Class.create(coreshop.order.sale.detail.abstractBlock, {
    saleInfo: null,

    initBlock: function () {
        var me = this;

        me.mailCorrespondenceStore = new Ext.data.JsonStore({
            data: []
        });

        me.mailCorrespondence = Ext.create('Ext.panel.Panel', {
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
                            xtype: 'actioncolumn',
                            sortable: false,
                            width: 50,
                            dataIndex: 'emailLogExistsHtml',
                            header: t('coreshop_mail_correspondence_mail_log'),
                            items: [{
                                tooltip: t('coreshop_mail_correspondence_mail_log_show'),
                                handler: function (grid, rowIndex) {
                                    var rec = grid.getStore().getAt(rowIndex),
                                        iFrameSettings = {width: 700, height: 500},
                                        iFrame = new Ext.Window(
                                            {
                                                title: t('coreshop_mail_correspondence_mail_log'),
                                                width: iFrameSettings.width,
                                                height: iFrameSettings.height,
                                                layout: 'fit',
                                                modal: true,
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
    },

    getPriority: function () {
        return 100;
    },

    getPosition: function () {
        return 'left';
    },

    getPanel: function () {
        return this.mailCorrespondence;
    },

    updateSale: function () {
        var me = this;

        me.mailCorrespondenceStore.loadRawData(me.sale.mailCorrespondence);
    }
});
