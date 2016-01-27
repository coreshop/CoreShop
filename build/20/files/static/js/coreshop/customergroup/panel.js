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


pimcore.registerNS("pimcore.plugin.coreshop.customergroup.panel");

pimcore.plugin.coreshop.customergroup.panel = Class.create({

    /**
     * @var string
     */
    layoutId: "coreshop_customer_groups_panel",

    /**
     * @var array
     */
    conditions: [],

    /**
     * @var array
     */
    actions: [],


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
                title: t("coreshop_customer_groups"),
                iconCls: "coreshop_icon_customer_groups",
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
            this.layout.updateLayout();
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getTreeNodeListeners: function () {

        return {
            "itemclick" : this.onTreeNodeClick.bind(this),
            "itemcontextmenu": this.onTreeNodeContextmenu.bind(this),
        };
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();

        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "pimcore_icon_delete",
            handler: this.deleteCustomerGroup.bind(this, record)
        }));
        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openCustomerGroup(record);
    },

    /**
     * return treelist
     * @returns {*}
     */
    getTree: function () {
        if (!this.tree) {
            var itemsPerPage = 30;

            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_Customergroup/list',
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
                rootVisible: false,
                store : this.store,
                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        appendOnly: false,
                        ddGroup: "element"
                    },
                    xtype: 'pimcoretreeview'
                },
                listeners: this.getTreeNodeListeners(),
                tbar: {
                    items: [
                        {
                            // add button
                            text: t("coreshop_customer_group_add"),
                            iconCls: "pimcore_icon_add",
                            handler: this.addCustomerGroup.bind(this)
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
    addCustomerGroup: function () {
        Ext.MessageBox.prompt(t('coreshop_customer_group_add'), t('coreshop_customer_group_enter_the_name_of_new'),
            this.addCustomerGroupComplete.bind(this), null, null, "");
    },


    /**
     * save added item
     * @param button
     * @param value
     * @param object
     * @todo ...
     */
    addCustomerGroupComplete: function (button, value, object) {
        if (button == "ok" && value.length > 2) {
            Ext.Ajax.request({
                url: "/plugin/CoreShop/admin_Customergroup/add",
                params: {
                    name: value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.tree.getStore().load();

                    if(!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openCustomerGroup(intval(data.id));
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
     * delete CustomerGroup
     */
    deleteCustomerGroup: function (record) {
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_CustomerGroup/delete",
            params: {
                id: record.get("id")
            },
            success: function () {
                this.store.load();
            }.bind(this)
        });
    },


    /**
     * open CustomerGroup
     * @param node
     */
    openCustomerGroup: function (node) {

        if(!is_numeric(node)) {
            node = node.id;
        }

        var panelKey = "coreshop_customer_group_" + node;

        if(this.panels[panelKey]) {
            this.panels[panelKey].activate();
        } else {
            Ext.Ajax.request({
                url: "/plugin/CoreShop/admin_CustomerGroup/get",
                params: {
                    id: node
                },
                success: function (response) {
                    var res = Ext.decode(response.responseText);
                    var item = new pimcore.plugin.coreshop.customergroup.item(this, res);

                    this.panels[panelKey] = item;
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
