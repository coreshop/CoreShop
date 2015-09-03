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
    },


    /**
     * activate panel
     */
    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.activate( this.layoutId );
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
            tabPanel.activate( this.layoutId );

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },


    /**
     * return treelist
     * @returns {*}
     */
    getTree: function () {
        if (!this.tree) {
            this.tree = new Ext.tree.TreePanel({
                region: "west",
                useArrows:true,
                autoScroll:true,
                animate:true,
                containerScroll: true,
                width: 200,
                split: true,
                root: {
                    nodeType: 'async',
                    id: '0'
                },
                loader: new Ext.tree.TreeLoader({
                    dataUrl: "/plugin/CoreShop/admin_Carrier/list",
                    requestMethod: "GET",
                    baseAttrs: {
                        listeners: {
                            click: this.openCarrier.bind(this),
                            contextmenu: function () {
                                this.select();

                                var menu = new Ext.menu.Menu();
                                menu.add(new Ext.menu.Item({
                                    text: t('delete'),
                                    iconCls: "pimcore_icon_delete",
                                    handler: this.attributes.reference.deleteCarrier.bind(this)
                                }));

                                menu.show(this.ui.getAnchor());
                            }
                        },
                        reference: this,
                        iconCls: "coreshop_carrier_icon",
                        leaf: true
                    }
                }),
                rootVisible: false,
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

                    this.tree.getRootNode().reload();

                    if(!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openCarrier(intval(data.id));
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
    deleteCarrier: function () {
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_Carrier/delete",
            params: {
                id: this.id
            },
            success: function () {
                this.attributes.reference.tree.getRootNode().reload();
            }.bind(this)
        });
    },


    /**
     * open carrier
     * @param node
     */
    openCarrier: function (node) {

        if(!is_numeric(node)) {
            node = node.id;
        }

        // load defined carrier
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_Carrier/get",
            params: {
                id: node
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                var item = new pimcore.plugin.coreshop.carrier.item(this, res);
            }.bind(this)
        });

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
