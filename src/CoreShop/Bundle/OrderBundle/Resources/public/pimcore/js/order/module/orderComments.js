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

pimcore.registerNS('coreshop.order.order.module.orderComments');
coreshop.order.order.module.orderComments = Class.create({
    initialize: function (sale) {
        this.layout = null;
        this.sale = sale;
    },

    getLayout: function () {

        if (this.layout !== null) {
            return this.layout;
        }

        this.layout = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_order_comments'),
            margin: '0 0 20 0',
            border: true,
            flex: 6,
            iconCls: 'coreshop_icon_order_comments',
            tools: [
                {
                    type: 'coreshop-add',
                    tooltip: t('add'),
                    handler: this.createComment.bind(this)
                }
            ],
        })

        this.loadList();

        return this.layout;
    },

    loadList: function () {

        this.layout.removeAll();
        this.layout.setLoading(t('loading'));

        Ext.Ajax.request({
            url: '/admin/coreshop/order-comment/list',
            params: {
                id: this.sale.o_id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                this.layout.setLoading(false);
                if (res.success) {
                    if (res.comments.length === 0) {
                        this.layout.add({
                            'xtype': 'panel',
                            'html': '<span class="coreshop-order-comments-nothing-found">' + t('coreshop_order_comments_nothing_found') + '</span>'
                        })
                    } else {
                        Ext.each(res.comments, function (comment) {
                            this.addCommentToList(comment);
                        }.bind(this));
                    }
                } else {
                    Ext.Msg.alert(t('error'), res.message);
                }

            }.bind(this)
        });

    },

    addCommentToList: function (comment) {

        var commentDate = Ext.Date.format(new Date(intval(comment.date) * 1000), 'd.m.Y H:i'),
            notificationApplied = comment.submitAsEmail === true;

        var commentPanel = {
            xtype: 'panel',
            bodyPadding: 10,
            margin: '0 0 10px 0',
            style: 'border-bottom: 1px dashed #b7b7b7;',
            title: commentDate + ' - ' + t('coreshop_order_comments_published_by') + ' ' + comment.userName,
            cls: 'coreshop-order-comment-block',
            tools: [
                {
                    type: 'coreshop-remove',
                    tooltip: t('add'),
                    handler: this.deleteComment.bind(this, comment)
                }
            ],
            items: [
                {
                    xtype: 'label',
                    style: 'display:block',
                    text: comment.text
                },
                {
                    xtype: 'label',
                    cls: notificationApplied ? 'comment_meta customer' : 'comment_meta admin',
                    text: notificationApplied ? t('coreshop_order_comments_notification_applied') : t('coreshop_order_comments_is_internal'),

                }
            ]
        };

        this.layout.add(commentPanel);
    },

    createComment: function (tab) {

        var _ = this,
            window = new Ext.window.Window({
                width: 600,
                height: 400,
                resizeable: false,
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
                            handler: _.saveComment.bind(_),
                            iconCls: 'pimcore_icon_apply'
                        }
                    ],
                    items: [
                        {
                            xtype: 'textarea',
                            name: 'comment',
                            style: "font-family: 'Courier New', Courier, monospace;",
                            width: '100%',
                            height: '70%',
                        },
                        {
                            xtype: 'checkbox',
                            name: 'submitAsEmail',
                            fieldLabel: t('coreshop_order_comment_trigger_notifications')

                        }
                    ]
                }]
            });

        window.show();
    },

    saveComment: function (btn, event) {

        var formWindow = btn.up('window'),
            form = formWindow.down('form').getForm();

        formWindow.setLoading(t('loading'));

        if (!form.isValid()) {
            return;
        }

        var formValues = form.getFieldValues();

        formValues['id'] = this.sale.o_id;

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
                        this.loadList();
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                } catch (e) {
                    formWindow.setLoading(false);
                }
            }.bind(this)
        });
    },

    deleteComment: function (comment, ev, el) {

        Ext.MessageBox.confirm(t('info'), t('coreshop_delete_order_comment_confirm'), function (buttonValue) {

            if (buttonValue === 'yes') {

                this.layout.setLoading(t('loading'));

                Ext.Ajax.request({
                    url: '/admin/coreshop/order-comment/delete',
                    method: 'post',
                    params: {
                        id: comment.id
                    },
                    callback: function (request, success, response) {
                        try {
                            this.layout.setLoading(false);
                            response = Ext.decode(response.responseText);
                            if (response.success === true) {
                                this.loadList();
                            } else {
                                Ext.Msg.alert(t('error'), response.message);
                            }
                        } catch (e) {
                            this.layout.setLoading(false);
                        }
                    }.bind(this)
                });
            }

        }.bind(this));
    }
});
