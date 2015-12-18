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


pimcore.registerNS("pimcore.plugin.coreshop.carriers.panel");

pimcore.plugin.coreshop.carriers.panel = Class.create({

    /**
     * @var string
     */
    layoutId: "coreshop_carriers",

    /**
     * constructor
     * @param layoutId
     */
    initialize: function() {
        // create layout
        this.getLayout();

        this.panels = [];
    },


    /**
     * activate panel
     */
    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem( this.layoutId );
    },


    /**
     * create tab panel
     * @returns Ext.Panel
     */
    getLayout: function () {

        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: this.layoutId,
                title: t("coreshop_carriers"),
                iconCls: "coreshop_icon_carriers",
                border: false,
                layout: "border",
                closable: true,

                // layout...
                items: [
                    this.getTree(),         // item tree, left side
                    this.getTabPanel()    // edit page, right side
                ]
            });

            // add event listener
            var layoutId = this.layoutId;
            this.layout.on("destroy", function () {
                pimcore.globalmanager.remove( layoutId );
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add( this.layout );
            tabPanel.setActiveItem( this.layoutId );

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
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
            iconCls: "pimcore_icon_delete",
            handler: this.deleteCarrier.bind(this, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openCarrier(record);
    },

    /**
     * return treelist
     * @returns {*}
     */
    getTree: function () {
        if (!this.tree) {
            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_Carrier/list',
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
                    nodeType: 'async',
                    id: '0'
                },
                rootVisible: false,
                store: this.store,
                listeners : this.getTreeNodeListeners(),
                tbar: {
                    items: [
                        {
                            // add button
                            text: t("coreshop_carrier_add"),
                            iconCls: "pimcore_icon_add",
                            handler: this.addCarrier.bind(this)
                        }
                    ]
                }
            });

            this.tree.on("render", function () {
                this.getRootNode().expand();
            });
        }

        return this.tree;
    },


    /**
     * add item popup
     */
    addCarrier: function () {
        Ext.MessageBox.prompt(t('coreshop_carrier_add'), t('coreshop_carrier_enter_the_name_of_the_carrier'),
            this.addCarrierComplete.bind(this), null, null, "");
    },


    /**
     * save added item
     * @param button
     * @param value
     * @param object
     * @todo ...
     */
    addCarrierComplete: function (button, value, object) {

        var regresult = value.match(/[a-zA-Z0-9_\-]+/);
        if (button == "ok" && value.length > 2 && regresult == value) {
            Ext.Ajax.request({
                url: "/plugin/CoreShop/admin_Carrier/add",
                params: {
                    name: value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.tree.getStore().reload();

                    if(!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openCarrier(data);
                    }
                }.bind(this)
            });
        } else if (button == "cancel") {
            return;
        }
        else {
            Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
        }
    },


    /**
     * delete existing carrier
     */
    deleteCarrier: function (record) {
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_Carrier/delete",
            params: {
                id: record.id
            },
            success: function () {
                this.tree.getStore().reload();
            }.bind(this)
        });
    },


    /**
     * open carrier
     * @param record
     */
    openCarrier: function (record) {
        var carrierPanelKey = "coreshop_carrier_" + record.id;

        if(this.panels[carrierPanelKey]) {
            this.panels[carrierPanelKey].activate();
        } else {

            // load defined carrier
            Ext.Ajax.request({
                url: "/plugin/CoreShop/admin_Carrier/get",
                params: {
                    id: record.id
                },
                success: function (response) {
                    var res = Ext.decode(response.responseText);
                    var item = new pimcore.plugin.coreshop.carrier.item(this, res);

                    this.panels[carrierPanelKey] = item;
                }.bind(this)
            });
        }
    },


    /**
     * @returns Ext.TabPanel
     */
    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.TabPanel({
                region: "center",
                border: false
            });
        }

        return this.panel;
    }
});
