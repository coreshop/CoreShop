/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.messaging.thread.item');
pimcore.plugin.coreshop.messaging.thread.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_messaging_thread',

    url : {
        save : '/plugin/CoreShop/admin_messaging-thread/save'
    },

    initPanel: function () {
        if (!pimcore.globalmanager.get('coreshop_messaging_thread_states').isLoaded()) {
            pimcore.globalmanager.get('coreshop_messaging_thread_states').load();
        }

        this.panel = this.getPanel();

        this.panel.on('beforedestroy', function () {
            delete this.parentPanel.panels[this.panelKey];
        }.bind(this));

        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.add(this.panel);
        tabPanel.setActiveItem(this.panel);
    },

    getItems : function () {
        return [this.getThreadPanel()];
    },

    getTitleText : function () {
        return t('coreshop_messaging_thread') + ' #' + this.data.thread.id;
    },

    getThreadPanel : function () {
        if (!this.threadPanel) {
            this.threadToolbar = Ext.create({
                xtype: 'toolbar',
                dock: 'top',
                items: this.getThreadStatusToolbar()
            });

            this.threadPanel = new Ext.Panel({
                region : 'center',
                scrollable : true,
                bodyPadding : 10,
                items : [
                    {
                        xtype : 'panel',
                        layout : {
                            type : 'vbox',
                            align: 'stretch'
                        },
                        dockedItems: [this.threadToolbar]
                    },
                    this.getMessagesPanel(),
                    this.getNewMessagePanel()
                ]
            });
        }

        return this.threadPanel;
    },

    getThreadStatusToolbar : function () {
        var items = [],
            me = this;

        for (var i = 0; i < pimcore.globalmanager.get('coreshop_messaging_thread_states').count(); i++) {
            var state = pimcore.globalmanager.get('coreshop_messaging_thread_states').getAt(i);

            var text = t('coreshop_messaging_thread_set_state') + ': ' + state.get('name');

            if (this.data.thread.statusId === state.getId()) {
                text = t('coreshop_messaging_thread_current_state') + ': ' + state.get('name');
            }

            items.push(
                {
                    xtype: 'button',
                    text : text,
                    disabled : this.data.thread.statusId === state.getId(),
                    state : state,
                    handler : function () {
                        me.changeStatus(this.config.state.getId());
                    }
                }
            );
        }

        if (this.data.thread.orderId) {
            items.push('->');
            items.push({
                xtype: 'button',
                iconCls: 'coreshop_icon_order',
                text : t('coreshop_order'),
                handler : function () {
                    pimcore.helpers.openObject(me.data.thread.orderId, 'object');
                }
            });
        } else if (this.data.thread.productId) {
            items.push('->');
            items.push({
                xtype: 'button',
                iconCls: 'coreshop_icon_product',
                text : t('coreshop_product'),
                handler : function () {
                    pimcore.helpers.openObject(me.data.thread.productId, 'object');
                }
            });
        }

        return items;
    },

    changeStatus : function (newStatus) {
        this.panel.mask(t('loading'));

        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_messaging-thread/change-status',
            params: {
                thread: this.data.thread.id,
                status : newStatus
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                this.panel.unmask();

                if (res.success) {
                    this.data.thread = res.data.thread;

                    pimcore.helpers.showNotification(t('success'), t('coreshop_save_success'), 'success');

                    this.threadToolbar.removeAll();
                    this.threadToolbar.add(this.getThreadStatusToolbar());
                } else {
                    pimcore.helpers.showNotification(t('error'), t('coreshop_messaging_new_message_error'), 'error');
                }
            }.bind(this)
        });
    },

    getMessagesPanel : function () {
        if (!this.messagesPanel) {
            var messages = [];

            for (var i = 0; i < this.data.messages.length; i++) {
                messages.push(this.getPanelForMessage(this.data.messages[i]));
            }

            this.messagesPanel = new Ext.Panel({
                flex : 1,
                scrollable : true,
                items : messages
            });
        }

        return this.messagesPanel;
    },

    getPanelForMessage : function (message) {
        var panel = new Ext.Panel({
            layout : {
                type : 'hbox',
                align : 'stretch'
            },
            bodyPadding : 10,
            padding : 10,
            items : [
                {
                    bodyPadding : 10,
                    width: 100,
                    html : '<img src="/plugins/CoreShop/static/img/48/messaging_' + (message.adminUserId ? 'admin' : 'user') + '.png" />'
                },
                {
                    flex : 1,
                    items : [
                        {
                            html : '<strong>' + (message.adminUserId ? this.getAdminUsername(message.admin) : message.user.email) + '</strong>'
                        },
                        {
                            html : '<i class="pimcore_icon_schedule coreshop_inline_icon"></i> ' + Ext.Date.format(new Date(message.creationDate * 1000), 'Y-m-d H:i:s')
                        },
                        {
                            bodyPadding : '10px 0',
                            bodyCls : 'coreshop_messaging_message',
                            html : message.message
                        }
                    ]
                }
            ]
        });

        return panel;
    },

    getAdminUsername : function (admin) {
        var name = admin.name;

        if (admin.firstname || admin.lastname || admin.email) {
            var addonName = '';

            if (admin.firstname) {
                addonName += admin.firstname + ' ';
            }

            if (admin.lastname) {
                addonName += admin.lastname + ' ';
            }

            if (admin.email) {
                if (addonName.length > 0) {
                    addonName += '- ';
                }

                addonName += admin.email + ' ';
            }

            name += ' (' + trim(addonName) + ')';
        }

        return name;
    },

    getNewMessagePanel : function () {
        if (!this.newMessagePanel) {

            var wysiwyg = new pimcore.object.tags.wysiwyg('', {
                height : 300,
                title :  t('coreshop_messaging_message')
            });

            this.newMessagePanel = new Ext.Panel({
                title : t('coreshop_messaging_new_message'),
                bodyPadding : 10,
                items : [
                    wysiwyg.getLayoutEdit()
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [
                        '->',
                        {
                            xtype : 'button',
                            text : t('coreshop_messaging_message_send'),
                            handler : function () {
                                this.sendNewMessage(wysiwyg.getValue());
                            }.bind(this)
                        }
                    ]
                }]
            });
        }

        return this.newMessagePanel;
    },

    sendNewMessage : function (content) {
        if (!content)
            return;

        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_messaging-thread/send-message',
            params: {
                thread: this.data.thread.id,
                message : content
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    this.messagesPanel.add(this.getPanelForMessage(res.data.newMessage));
                } else {
                    pimcore.helpers.showNotification(t('error'), t('coreshop_messaging_new_message_error'), 'error');
                }
            }.bind(this)
        });
    }
});
