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

pimcore.registerNS('coreshop.resource.panel');
coreshop.resource.panel = Class.create({

    layoutId: 'abstract_layout',
    storeId: 'abstract_store',
    iconCls: 'coreshop_abstract_icon',
    type: 'abstract',

    url: {
        add: '',
        delete: '',
        get: '',
        list: ''
    },

    initialize: function () {
        // create layout
        this.getLayout();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: this.layoutId,
                title: this.getTitle(),
                iconCls: this.iconCls,
                border: false,
                layout: 'border',
                closable: true,
                items: this.getItems()
            });

            // add event listener
            var layoutId = this.layoutId;
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove(layoutId);
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

    getTitle: function () {
        return t(this.type);
    },

    refresh: function () {
        if (pimcore.globalmanager.exists(this.storeId)) {
            pimcore.globalmanager.get(this.storeId).load();
        }
    },

    getItems: function () {
        return [this.getNavigation(), this.getTabPanel()];
    },

    getDefaultGridDisplayColumnName: function() {
        return 'name';
    },

    getGridDisplayColumnRenderer: function (value, metadata, record) {
        metadata.tdAttr = 'data-qtip="ID: ' + record.get('id') + '"';
        return value;
    },

    getDefaultGridConfiguration: function () {
        return {
            region: 'west',
            store: pimcore.globalmanager.get(this.storeId),
            columns: [
                {
                    text: '',
                    dataIndex: this.getDefaultGridDisplayColumnName(),
                    flex: 1,
                    renderer: this.getGridDisplayColumnRenderer
                }
            ],
            listeners: this.getTreeNodeListeners(),
            useArrows: true,
            autoScroll: true,
            animate: true,
            containerScroll: true,
            width: 200,
            split: true,
            tbar: this.getTopBar(),
            bbar: {
                items: [{
                    xtype: 'label',
                    text: '',
                    itemId: 'totalLabel'
                }, '->', {
                    iconCls: 'pimcore_icon_reload',
                    scale: 'small',
                    handler: function () {
                        this.grid.getStore().load();
                    }.bind(this)
                }]
            },
            hideHeaders: true
        };
    },

    getGridConfiguration: function () {
        return [];
    },

    getNavigation: function () {
        if (!this.grid) {

            this.grid = Ext.create('Ext.grid.Panel',
                Ext.apply({},
                    this.getGridConfiguration(),
                    this.getDefaultGridConfiguration()
                )
            );

            this.grid.getStore().on('load', function (store, records) {
                if (this.grid.rendered) {
                    this.grid.down('#totalLabel').setText(t('coreshop_total_items').format(records.length));
                }
            }.bind(this));

            this.grid.on('beforerender', function () {
                this.getStore().load();
            });

        }

        return this.grid;
    },

    getTopBar: function () {
        return [
            {
                // add button
                text: t('add'),
                iconCls: 'pimcore_icon_add',
                handler: this.addItem.bind(this)
            }
        ];
    },

    getTreeNodeListeners: function () {
        return {
            itemclick: this.onTreeNodeClick.bind(this),
            itemcontextmenu: this.onTreeNodeContextmenu.bind(this)
        };
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: 'pimcore_icon_delete',
            handler: this.deleteItem.bind(this, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts) {
        this.openItem(record.data);
    },

    addItem: function () {
        Ext.MessageBox.prompt(t('add'), t('coreshop_enter_the_name'), this.addItemComplete.bind(this), null, null, '');
    },

    addItemComplete: function (button, value, object) {
        var jsonData = {
            name: value
        };

        if (Ext.isFunction(this.prepareAdd)) {
            jsonData = this.prepareAdd(jsonData);
        }

        if (button === 'ok' && value.length > 2) {
            Ext.Ajax.request({
                url: this.url.add,
                jsonData: jsonData,
                method: 'post',
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.grid.getStore().reload();

                    this.refresh();

                    if (!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openItem(data.data);
                    }
                }.bind(this)
            });
        } else {
            Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
        }
    },

    deleteItem: function (record) {
        Ext.Ajax.request({
            url: this.url.delete,
            method: 'DELETE',
            params: {
                id: record.id
            },
            success: function () {
                this.grid.getStore().reload();

                this.refresh();

                if (this.panels[this.getPanelKey(record)]) {
                    this.panels[this.getPanelKey(record)].destroy();
                }

            }.bind(this)
        });
    },

    getPanelKey: function (record) {
        return this.layoutId + record.id;
    },

    openItem: function (record) {
        var panelKey = this.getPanelKey(record);

        if (this.panels[panelKey]) {
            this.panels[panelKey].activate();
        }
        else {
            Ext.Ajax.request({
                url: this.url.get,
                params: {
                    id: record.id
                },
                success: function (response) {
                    var res = Ext.decode(response.responseText);

                    if (res.success) {
                        var itemClass = this.getItemClass();

                        this.panels[panelKey] = new itemClass(this, res.data, panelKey, this.type, this.storeId);
                    } else {
                        Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                    }

                }.bind(this)
            });
        }
    },

    getItemClass: function () {
        return coreshop[this.type].item;
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.TabPanel({
                region: 'center',
                border: false
            });
        }

        return this.panel;
    }
});
