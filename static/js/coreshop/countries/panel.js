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

pimcore.registerNS("pimcore.plugin.coreshop.countries.panel");
pimcore.plugin.coreshop.countries.panel = Class.create({

    initialize: function () {
        this.getTabPanel();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("coreshop_country");
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
                items: [this.getTree(), this.getEditPanel()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("coreshop_country");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_country");
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
            iconCls: "coreshop_icon_country_remove",
            listeners: {
                "click": this.removeCountry.bind(this, record)
            }
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openCountry(record.id, item);
    },

    getTree: function () {
        if (!this.tree) {
            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_country/get-countries',
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
                    text: t('coreshop_country_add'),
                    iconCls: 'coreshop_icon_country_add',
                    handler : this.addCountry.bind(this)
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
                this.tree.getStore().reload();
                this.openCountry(data.country.id);
            } else {
                pimcore.helpers.showNotification(t("error"), t("coreshop_country_creation_error"), "error", t(data.message));
            }

        } catch(e){
            pimcore.helpers.showNotification(t("error"), t("coreshop_country_creation_error"), "error");
        }
    },

    removeCountry : function(record, item)
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
                        url: "/plugin/CoreShop/admin_country/remove",
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