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

pimcore.registerNS('coreshop.order.sale.detail.blocks.comments');
coreshop.order.order.detail.blocks.comments = Class.create(coreshop.order.sale.detail.abstractBlock, {
    saleInfo: null,

    initBlock: function () {
        var me = this;

        me.layout = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_order_comments'),
            margin: '0 0 20 0',
            border: true,
            flex: 6,
            iconCls: 'coreshop_icon_order_comments',
            tools: [
                {
                    type: 'coreshop-add',
                    tooltip: t('add'),
                    handler: me.createComment.bind(me)
                }
            ]
        });
    },

    loadList: function () {
        var me = this;

        me.layout.removeAll();
        me.layout.setLoading(t('loading'));

        Ext.Ajax.request({
            url: '/admin/coreshop/order-comment/list',
            params: {
                id: me.sale.o_id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                me.layout.setLoading(false);

                if (res.success) {
                    if (res.comments.length === 0) {
                        me.layout.add({
                            'xtype': 'panel',
                            'html': '<span class="coreshop-order-comments-nothing-found">' + t('coreshop_order_comments_nothing_found') + '</span>'
                        })
                    } else {
                        Ext.each(res.comments, function (comment) {
                            me.addCommentToList(comment);
                        });
                    }
                } else {
                    Ext.Msg.alert(t('error'), res.message);
                }

            }
        });
    },

    addCommentToList: function (comment) {
        var me = this,
            commentDate = Ext.Date.format(new Date(intval(comment.date) * 1000), 'd.m.Y H:i'),
            notificationApplied = comment.submitAsEmail === true;

        var commentPanel = {
            xtype: 'panel',
            bodyPadding: 10,
            margin: '0 0 10px 0',
            style: 'border-bottom: 1px dashed #b7b7b7;',
            title: commentDate + ' - <span class="published-by">' + t('coreshop_order_comments_published_by') + ' ' + comment.userName + '</span>',
            cls: 'coreshop-order-comment-block',
            tools: [
                {
                    type: 'coreshop-remove',
                    tooltip: t('add'),
                    handler: me.deleteComment.bind(me, comment)
                }
            ],
            items: [
                {
                    xtype: 'label',
                    style: 'display:block',
                    html: comment.text
                },
                {
                    xtype: 'label',
                    cls: notificationApplied ? 'comment_meta external' : 'comment_meta internal',
                    text: notificationApplied ? t('coreshop_order_comments_notification_applied') : t('coreshop_order_comments_is_internal'),
                }
            ]
        };

        me.layout.add(commentPanel);
    },

    createComment: function (tab) {
        var me = this,
            noteLabel = new Ext.form.Label({
                flex: 1,
                text: t('coreshop_order_comment_customer_locale_note') + ' ' + me.sale.localeCode,
                style: 'color: gray; font-style: italic; text-align: right; padding: 0px 2px 0px 0px;',
                hidden: true
            }),
            window = new Ext.window.Window({
                width: 600,
                height: 400,
                resizeable: false,
                modal: true,
                layout: 'fit',
                title: t('coreshop_order_comment_create'),
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
                            text: t('coreshop_order_comment_create'),
                            handler: me.saveComment.bind(me),
                            iconCls: 'pimcore_icon_apply'
                        }
                    ],
                    items: [
                        {
                            xtype: 'textarea',
                            name: 'comment',
                            fieldLabel: t('coreshop_order_comment'),
                            labelAlign: 'top',
                            width: '100%',
                            height: '70%',
                        },
                        {
                            xtype: 'fieldcontainer',
                            layout: 'hbox',
                            border: 0,
                            style: {
                                border: 0
                            },
                            items: [
                                {
                                    xtype: 'checkbox',
                                    flex: 2,
                                    name: 'submitAsEmail',
                                    fieldLabel: t('coreshop_order_comment_trigger_notifications'),
                                    listeners: {
                                        'change': function (b) {
                                            noteLabel.setHidden(!b.checked)
                                        }
                                    }
                                },
                                noteLabel
                            ]
                        }
                    ]
                }]
            });

        window.show();
    },

    saveComment: function (btn, event) {
        var me = this,
            formWindow = btn.up('window'),
            form = formWindow.down('form').getForm();

        formWindow.setLoading(t('loading'));

        if (!form.isValid()) {
            return;
        }

        var formValues = form.getFieldValues();

        formValues['id'] = me.sale.o_id;

        Ext.Ajax.request({
            url: '/admin/coreshop/order-comment/add',
            method: 'post',
            params: formValues,
            callback: function (request, success, response) {
                try {
                    formWindow.setLoading(false);
                    response = Ext.decode(response.responseText);
                    if (response.success === true) {
                        formWindow.close();
                        formWindow.destroy();
                        me.loadList();
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                } catch (e) {
                    formWindow.setLoading(false);
                }
            }
        });
    },

    deleteComment: function (comment, ev, el) {
        var me = this;

        Ext.MessageBox.confirm(t('info'), t('coreshop_delete_order_comment_confirm'), function (buttonValue) {

            if (buttonValue === 'yes') {

                me.layout.setLoading(t('loading'));

                Ext.Ajax.request({
                    url: '/admin/coreshop/order-comment/delete',
                    method: 'post',
                    params: {
                        id: comment.id
                    },
                    callback: function (request, success, response) {
                        me.layout.setLoading(false);

                        try {
                            response = Ext.decode(response.responseText);
                            if (response.success === true) {
                                me.loadList();
                            } else {
                                Ext.Msg.alert(t('error'), response.message);
                            }
                        } catch (e) {

                        }
                    }
                });
            }

        });
    },

    getPriority: function () {
        return 20;
    },

    getPosition: function () {
        return 'right';
    },

    getPanel: function () {
        return this.layout;
    },

    updateSale: function () {
        var me = this;

        me.loadList();
    }
});
