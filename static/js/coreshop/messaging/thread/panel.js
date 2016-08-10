/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.messaging.thread.panel');
pimcore.plugin.coreshop.messaging.thread.panel = Class.create(pimcore.plugin.coreshop.messaging.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_messaging_thread_panel',
    storeId : null,
    iconCls : 'coreshop_icon_messaging_thread',
    type : 'thread',

    url : {
        delete : '/plugin/CoreShop/admin_messaging-thread/delete',
        get : '/plugin/CoreShop/admin_messaging-thread/get',
        list : '/plugin/CoreShop/admin_messaging-thread/list'
    },

    initialize : function () {
        pimcore.globalmanager.get('coreshop_messaging_contacts').load();
        pimcore.globalmanager.get('coreshop_messaging_thread_states').load();

        this.getLayout();

        this.panels = [];
    },

    getItems : function () {
        this.contactsPanel = new Ext.Panel({
            layout : {
                type: 'hbox',
                align : 'stretch'
            },
            bodyPadding: 10,
            items : [],
            height : 200
        });

        this.panel = new Ext.Panel(
            {
                region: 'center',
                layout : 'fit',
                border: false,
                scrollable : true,
                items : {
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    items: [
                        this.contactsPanel,
                        this.getGrid()
                    ]
                }
            }
        );

        this.renderContacts();

        return [this.panel];
    },

    getTreeNodeListeners: function () {
        return {
            itemclick : this.onTreeNodeClick.bind(this)
        };
    },

    getGrid : function () {
        if (!this.grid) {
            //var store = pimcore.globalmanager.get(this.storeId);
            this.store = new Ext.data.JsonStore({
                remoteSort: true,
                remoteFilter: true,
                autoDestroy: true,
                autoSync: true,
                pageSize: pimcore.helpers.grid.getDefaultPageSize(),
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_messaging-thread/list',
                    reader: {
                        type: 'json',
                        rootProperty: 'data',
                        totalProperty : 'total'
                    }
                },

                //alternatively, a Ext.data.Model name can be given (see Ext.data.Store for an example)
                fields: [
                    { name:'id' },
                    { name:'user' },
                    { name:'email' },
                    { name:'language' },
                    { name:'contact' },
                    { name:'status' },
                    { name:'admin' },
                    { name:'messages' },
                    { name:'shopId' }
                ]
            });

            var columns = [
                {
                    text: t('id'),
                    dataIndex: 'id',
                    width : 60,
                    filter : 'numeric'
                },
                {
                    text: t('coreshop_token'),
                    dataIndex : 'token',
                    width : 120,
                    filter : 'string'
                },
                {
                    text: t('reference'),
                    dataIndex : 'reference',
                    width : 150
                },
                {
                    text: t('user'),
                    dataIndex : 'user',
                    width: 200
                },
                {
                    text: t('email'),
                    dataIndex : 'email',
                    width: 200,
                    filter : 'string'
                },
                {
                    text: t('coreshop_messaging_contact'),
                    dataIndex : 'contactId',
                    width: 200,
                    filter: {
                        type : 'list',
                        store : pimcore.globalmanager.get('coreshop_messaging_contacts')
                    },
                    renderer: function (value, metadata, record)
                    {
                        var contact = pimcore.globalmanager.get('coreshop_messaging_contacts').getById(value);

                        if (contact) {
                            return contact.get('name');
                        }

                        return value;
                    }
                },
                {
                    text: t('language'),
                    dataIndex : 'language',
                    width: 100,
                    filter : 'string'
                },
                {
                    text: t('coreshop_messaging_threadstate'),
                    dataIndex : 'statusId',
                    width: 200,
                    renderer: function (value, metadata, record)
                    {
                        var state = pimcore.globalmanager.get('coreshop_messaging_thread_states').getById(value);

                        if (state) {
                            var bgColor = state.get('color');

                            if (bgColor) {
                                var textColor = (parseInt(bgColor.replace('#', ''), 16) > 0xffffff / 2) ? 'black' : 'white';

                                return '<span class="rounded-color" style="background-color:' + bgColor + '; color: ' + textColor + '">' + state.get('name') + '</span>';
                            }

                            return state.get('name');
                        }

                        return value;
                    },

                    filter: {
                        type : 'list',
                        store : pimcore.globalmanager.get('coreshop_messaging_thread_states')
                    }
                },
                {
                    text: t('admin'),
                    dataIndex : 'admin',
                    width: 200
                },
                {
                    text: t('coreshop_messaging_messages'),
                    dataIndex : 'messages',
                    flex : 1
                }
            ];

            if (coreshop.settings.multishop) {
                columns.splice(1, 0, {
                    text: t('coreshop_shop'),
                    dataIndex: 'shopId',
                    filter: {
                        type : 'list',
                        store : pimcore.globalmanager.get('coreshop_messaging_thread_states')
                    },
                    renderer : function (val) {
                        var store = pimcore.globalmanager.get('coreshop_shops');
                        var pos = store.findExact('id', String(val));
                        if (pos >= 0) {
                            var shop = store.getAt(pos);

                            return shop.get('name');
                        }

                        return null;
                    }
                });
            }

            this.grid = Ext.create('Ext.grid.Panel', {
                store: this.store,
                listeners : this.getTreeNodeListeners(),
                plugins: {
                    ptype : 'pimcore.gridfilters',
                    pluginId : 'filter',
                    encode: true,
                    local: false
                },
                columns: columns,
                flex : 1,
                bbar: this.pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(this.store),
            });

            this.grid.on('beforerender', function () {
                this.getStore().load();
            });

        }

        return this.grid;
    },

    filterGrid : function (field, value, clearFilter) {
        if (clearFilter === undefined)
            clearFilter = false;

        if (clearFilter) {
            this.grid.getPlugin('filter').clearFilters();
        }

        this.grid.getPlugin('filter').addFilter({ dataIndex : field, value : value });
    },

    renderContacts : function () {
        var me = this;

        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_messaging-thread/get-contacts-with-message-count',
            success: function (response) {
                var res = Ext.decode(response.responseText);
                if (res.success) {
                    for (var i = 0; i < res.data.length; i++) {
                        var record = res.data[i];

                        this.contactsPanel.add({
                            padding : 5,
                            xtype : 'panel',
                            height: 200,
                            width : '33.3%',
                            title : record.name,
                            items : [
                                {
                                    html : '<p>' + record.description + '</p>'
                                },
                                {
                                    xtype : 'button',
                                    text : record.count + ' ' + t('coreshop_messaging_new_messages'),
                                    record : record,
                                    handler : function () {
                                        me.filterGrid('contactId', this.config.record.id, true);
                                    }
                                }
                            ]
                        });
                    }

                    this.renderStatesStats();
                }
            }.bind(this)
        });
    },

    renderStatesStats : function () {
        var panel = {
            padding : 5,
            xtype : 'panel',
            height: 200,
            width : '33.3%',
            title : t('coreshop_messaging_states_statistics')
        };

        var panelEntries = [];

        for (var i = 0; i < pimcore.globalmanager.get('coreshop_messaging_thread_states').count(); i++) {
            var record = pimcore.globalmanager.get('coreshop_messaging_thread_states').getAt(i);

            panelEntries.push({
                bodyCls : 'badge-container',
                html : record.get('name') + ' <span class="badge">' + record.get('count') + '</span>'
            });
        }

        panel.items = panelEntries;

        this.contactsPanel.add(panel);
    }
});
