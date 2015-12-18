/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

pimcore.registerNS("pimcore.plugin.coreshop.zones.panel");
pimcore.plugin.coreshop.zones.panel = Class.create({

    initialize: function () {
        this.getTabPanel();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("coreshop_zones_panel");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "coreshop_zones_panel",
                iconCls: "coreshop_icon_zone",
                title: t("coreshop_zones"),
                border: false,
                layout: "border",
                closable:true,
                items: [this.getTree(), this.getEditPanel()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("coreshop_zones_panel");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_zones_panel");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getTreeNodeListeners: function () {

        return {
            "itemclick" : this.onTreeNodeClick.bind(this),
            "itemcontextmenu": this.onTreeNodeContextmenu.bind(this)
        };
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();

        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "coreshop_icon_zone_remove",
            listeners: {
                "click": this.removeZone.bind(this, record)
            }
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openZone(record.id, item);
    },

    getTree: function () {
        if (!this.tree) {
            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_zone/get-zones',
                    reader: {
                        type: 'json'

                    },
                    extraParams: {
                        grouped: 1
                    }
                }
            });


            this.tree = Ext.create('pimcore.tree.Panel', {
                region: "west",
                useArrows: true,
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 200,
                split: true,
                root: {
                    id: "0",
                    root: true
                },
                rootVisible: false,
                store: this.store,
                listeners : this.getTreeNodeListeners(),
                tbar : [{
                    text: t('coreshop_zone_add'),
                    iconCls: 'coreshop_icon_zone_add',
                    handler : this.addZone.bind(this)
                }],
            });

            this.tree.on("render", function () {
                this.getRootNode().expand();
            });
        }

        return this.tree;
    },


    getEditPanel: function () {
        if (!this.editPanel) {
            this.editPanel = new Ext.TabPanel({
                activeTab: 0,
                items: [],
                region: 'center'
            });
        }

        return this.editPanel;
    },

    addZone : function() {
        Ext.MessageBox.prompt(t('add'), t('please_enter_the_name'), function (button, value, object) {
            if(button=='ok' && value != ''){
                Ext.Ajax.request({
                    url: "/plugin/CoreShop/admin_zone/add",
                    params: {
                        name: value
                    },
                    success: this.addZoneComplete.bind(this)
                });
            }
        }.bind(this));
    },

    addZoneComplete : function(transport) {
        try{
            var data = Ext.decode(transport.responseText);

            if(data && data.success){
                this.tree.getStore().reload();
                this.openZone(data.zone.id);
            } else {
                pimcore.helpers.showNotification(t("error"), t("coreshop_zone_creation_error"), "error", t(data.message));
            }

        } catch(e){
            pimcore.helpers.showNotification(t("error"), t("coreshop_zone_creation_error"), "error");
        }
    },

    removeZone: function(record, item)
    {
        Ext.MessageBox.show({
            title: t('delete'),
            msg: t("are_you_sure"),
            buttons: Ext.Msg.OKCANCEL ,
            icon: Ext.MessageBox.QUESTION,
            fn: function (button)
            {
                if (button == "ok")
                {
                    Ext.Ajax.request({
                        url: "/plugin/CoreShop/admin_zone/remove",
                        params: {
                            id: record.id
                        },
                        success: function() {
                            this.tree.getStore().reload();
                        }.bind(this)
                    });
                }
            }.bind(this)
        });
    },

    openZone: function(zoneId) {
        var zonePanelKey = "coreshop_zone_" + zoneId;
        if(this.panels[zonePanelKey]) {
            this.panels[zonePanelKey].activate();
        } else {
            var zonePanel = new pimcore.plugin.coreshop.zones.zone(this, zoneId);
            this.panels[zonePanelKey] = zonePanel;
        }

    },
});