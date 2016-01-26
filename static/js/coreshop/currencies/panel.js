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

pimcore.registerNS("pimcore.plugin.coreshop.currencies.panel");
pimcore.plugin.coreshop.currencies.panel = Class.create({

    initialize: function () {
        this.getTabPanel();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("coreshop_currencies_panel");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "coreshop_currencies_panel",
                iconCls: "coreshop_icon_currency",
                title: t("coreshop_currencies"),
                border: false,
                layout: "border",
                closable:true,
                items: [this.getTree(), this.getEditPanel()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("coreshop_currencies_panel");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_currencies_panel");
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
            iconCls: "coreshop_icon_currency_remove",
            listeners: {
                "click": this.removeCurrency.bind(this, record)
            }
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openCurrency(record.id);
    },

    getTree: function () {
        if (!this.tree) {
            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_currency/list',
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
                    text: t('coreshop_currency_add'),
                    iconCls: 'coreshop_icon_currency_add',
                    handler : this.addCurrency.bind(this)
                }]
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

    addCurrency : function() {
        Ext.MessageBox.prompt(t('add'), t('please_enter_the_name'), function (button, value, object) {
            if(button=='ok' && value != ''){
                Ext.Ajax.request({
                    url: "/plugin/CoreShop/admin_currency/add",
                    params: {
                        name: value
                    },
                    success: this.addCurrencyComplete.bind(this)
                });
            }
        }.bind(this));
    },

    addCurrencyComplete : function(transport) {
        try{
            var data = Ext.decode(transport.responseText);

            if(data && data.success){
                this.tree.getStore().reload();
                this.openCurrency(data.currency.id);
            } else {
                pimcore.helpers.showNotification(t("error"), t("coreshop_currency_creation_error"), "error", t(data.message));
            }

        } catch(e){
            pimcore.helpers.showNotification(t("error"), t("coreshop_currency_creation_error"), "error");
        }
    },

    removeCurrency : function(record) {
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
                        url: "/plugin/CoreShop/admin_currency/remove",
                        params: {
                            id: record.id
                        },
                        success: function() {
                            this.tree.getStore().reload()
                        }.bind(this)
                    });
                }
            }.bind(this)
        });
    },

    openCurrency: function(currencyId) {
        var currencyPanelKey = "coreshop_currency_" + currencyId;
        if(this.panels[currencyPanelKey]) {
            this.panels[currencyPanelKey].activate();
        } else {
            var currencyPanel = new pimcore.plugin.coreshop.currencies.currency(this, currencyId);
            this.panels[currencyPanelKey] = currencyPanel;
        }

    }
});