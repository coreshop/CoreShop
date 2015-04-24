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

pimcore.registerNS("pimcore.plugin.coreshop.currencies.panel");
pimcore.plugin.coreshop.currencies.panel = Class.create({

    initialize: function () {
        this.getTabPanel();

        this.panels = [];
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.activate("coreshop_currency");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "coreshop_currency",
                iconCls: "coreshop_icon_currency",
                title: t("coreshop_currencies"),
                border: false,
                layout: "border",
                closable:true,
                items: [this.getLayout()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.activate("coreshop_currency");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_currency");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getLayout: function ()
    {
        var tree = new Ext.tree.TreePanel({
            xtype: "treepanel",
            region: "west",
            title: t("coreshop_currencies"),
            width: 200,
            enableDD: false,
            autoScroll: true,
            collapsible: true,
            rootVisible: false,
            root: {
                id: "0",
                root: true
            },
            loader: new Ext.tree.TreeLoader({
                dataUrl: '/plugin/CoreShop/admin_currency/get-currencies',
                requestMethod: "GET",
                baseAttrs: {
                    reference: this,
                    allowChildren: true,
                    isTarget: true,
                    listeners : {
                        'click' : this.onTreeNodeClick.bind(this)
                    }
                }
            }),
            bodyStyle: "padding: 5px;"
        });

        return [tree, this.getEditPanel()];
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
            this.openCurrency(node.id);
        }
    },

    openCurrency: function(currencyId) {
        var currencyPanelKey = "coreshop_currency_" + currencyId;
        if(this.panels[currencyPanelKey]) {
            this.panels[currencyPanelKey].activate();
        } else {
            var currencyPanel = new pimcore.plugin.coreshop.currencies.currency(this, currencyId);
            this.panels[currencyPanelKey] = currencyPanel;
        }

    },
});