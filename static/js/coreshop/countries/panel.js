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

pimcore.registerNS("pimcore.plugin.coreshop.countries.panel");
pimcore.plugin.coreshop.countries.panel = Class.create({

    initialize: function () {
        this.getTabPanel();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.activate("coreshop_country");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "coreshop_country",
                iconCls: "coreshop_icon_country",
                title: t("coreshop_countries"),
                border: false,
                layout: "border",
                closable:true,
                items: [this.getLayout()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.activate("coreshop_country");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_country");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getTreeNodeListeners: function () {
        var treeNodeListeners = {
            'click' : this.onTreeNodeClick.bind(this),
            "contextmenu": this.onTreeNodeContextmenu
        };

        return treeNodeListeners;
    },

    getLayout: function ()
    {
        this.tree = new Ext.tree.TreePanel({
            xtype: "treepanel",
            region: "west",
            title: t("coreshop_countries"),
            width: 200,
            enableDD: false,
            autoScroll: true,
            collapsible: true,
            rootVisible: false,
            root: {
                id: "0",
                root: true
            },
            tbar : [{
                text: t('coreshop_country_add'),
                iconCls: 'coreshop_icon_country_add',
                handler : this.addCountry.bind(this)
            }],
            loader: new Ext.tree.TreeLoader({
                dataUrl: '/plugin/CoreShop/admin_country/get-countries',
                requestMethod: "GET",
                baseAttrs: {
                    reference: this,
                    allowChildren: true,
                    isTarget: true,
                    listeners : this.getTreeNodeListeners()
                }
            }),
            bodyStyle: "padding: 5px;"
        });

        return [this.tree, this.getEditPanel()];
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

    onTreeNodeClick: function (node) {

        if(!node.attributes.allowChildren && node.id > 0) {
            this.openCountry(node.id);
        }
    },

    onTreeNodeContextmenu: function () {

        this.select();
        var menu = new Ext.menu.Menu();

        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "coreshop_icon_country_remove",
            listeners: {
                "click": this.attributes.reference.removeCountry.bind(this)
            }
        }));

        if(typeof menu.items != "undefined" && typeof menu.items.items != "undefined"
            && menu.items.items.length > 0) {
            menu.show(this.ui.getAnchor());
        }
    },

    addCountry : function() {
        Ext.MessageBox.prompt(t('add'), t('please_enter_the_name'), function (button, value, object) {
            if(button=='ok' && value != ''){
                Ext.Ajax.request({
                    url: "/plugin/CoreShop/admin_country/add",
                    params: {
                        name: value
                    },
                    success: this.addCountryComplete.bind(this)
                });
            }
        }.bind(this));
    },

    addCountryComplete : function(transport) {
        try{
            var data = Ext.decode(transport.responseText);

            if(data && data.success){
                this.tree.root.reload();
                this.openCountry(data.country.id);
            } else {
                pimcore.helpers.showNotification(t("error"), t("coreshop_country_creation_error"), "error", t(data.message));
            }

        } catch(e){
            pimcore.helpers.showNotification(t("error"), t("coreshop_country_creation_error"), "error");
        }
    },

    removeCountry : function() {
        Ext.MessageBox.show({
            title: t('delete'),
            msg: t("are_you_sure"),
            buttons: Ext.Msg.OKCANCEL ,
            icon: Ext.MessageBox.QUESTION,
            fn: function (button) {
                if (button == "ok") {
                    Ext.Ajax.request({
                        url: "/plugin/CoreShop/admin_country/remove",
                        params: {
                            id: this.id
                        },
                        success: function() {
                            this.remove();
                        }.bind(this)
                    });
                }
            }.bind(this)
        });
    },

    openCountry: function(countryId) {
        var countryPanelKey = "coreshop_country_" + countryId;
        if(this.panels[countryPanelKey]) {
            this.panels[countryPanelKey].activate();
        } else {
            var countryPanel = new pimcore.plugin.coreshop.countries.country(this, countryId);
            this.panels[countryPanelKey] = countryPanel;
        }

    },
});