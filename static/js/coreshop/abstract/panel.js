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


pimcore.registerNS("pimcore.plugin.coreshop.abstract.panel");

pimcore.plugin.coreshop.abstract.panel = Class.create({

    /**
     * @var string
     */

    layoutId: "abstract_layout",
    storeId : "abstract_store",
    iconCls : "coreshop_abstract_icon",
    type : "abstract",

    url : {
        add : "",
        delete : "",
        get : "",
        list : ""
    },

    /**
     * constructor
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
                title: t("coreshop_" + this.type),
                iconCls: this.iconCls,
                border: false,
                layout: "border",
                closable: true,
                items: this.getItems()
            });

            // add event listener
            var layoutId = this.layoutId;
            this.layout.on("destroy", function () {
                pimcore.globalmanager.remove(layoutId);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getItems : function() {
        return [this.getTree(), this.getTabPanel()];
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
                    url: this.url.list,
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
                            text: t("coreshop_"+this.type+"_add"),
                            iconCls: "pimcore_icon_add",
                            handler: this.addItem.bind(this)
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
            handler: this.deleteItem.bind(this, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openItem(record.data);
    },


    /**
     * add item popup
     */
    addItem: function () {
        Ext.MessageBox.prompt(t('coreshop_' + this.type + '_add'), t('coreshop_'+this.type+'_enter_the_name'),
            this.addItemComplete.bind(this), null, null, "");
    },


    /**
     * save added item
     * @param button
     * @param value
     * @param object
     * @todo ...
     */
    addItemComplete: function (button, value, object) {

        var regresult = value.match(/[a-zA-Z0-9_\-]+/);
        if (button == "ok" && value.length > 2 && regresult == value) {
            Ext.Ajax.request({
                url: this.url.add,
                params: {
                    name: value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.tree.getStore().reload();

                    if(!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openItem(data.data);
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
     * delete existing item
     */
    deleteItem: function (record) {
        Ext.Ajax.request({
            url: this.url.delete,
            params: {
                id: record.id
            },
            success: function () {
                this.tree.getStore().reload();
            }.bind(this)
        });
    },


    /**
     * open item
     * @param record
     */
    openItem: function (record) {
        var panelKey = this.layoutId + record.id;

        if(this.panels[panelKey])
        {
            this.panels[panelKey].activate();
        }
        else
        {
            Ext.Ajax.request({
                url: this.url.get,
                params: {
                    id: record.id
                },
                success: function (response) {
                    var res = Ext.decode(response.responseText);

                    if(res.success) {
                        this.panels[panelKey] = new pimcore.plugin.coreshop[this.type].item(this, res.data, panelKey, this.type);
                    }
                    else {
                        //TODO: Show messagebox
                        Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                    }

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
