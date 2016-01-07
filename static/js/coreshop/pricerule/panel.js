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


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.panel");

pimcore.plugin.coreshop.pricerule.panel = Class.create({

    /**
     * @var string
     */
    layoutId: "coreshop_price_rules",

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
        // load defined conditions & actions
        var _this = this;
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_PriceRules/get-config",
            method: "GET",
            success: function(result){
                var config = Ext.decode(result.responseText);
                _this.condition = config.conditions;
                _this.action = config.actions;
            }
        });

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
                title: t("coreshop_price_rules"),
                iconCls: "coreshop_icon_price_rule",
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
            "itemmove": this.onTreeNodeMove.bind(this)
        };
    },

    onTreeNodeMove: function (node, oldParent, newParent, index, eOpts ) {
        var tree = node.getOwnerTree();

        tree.getDockedItems()[0].items.get("btnSave").show();
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();

        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "pimcore_icon_delete",
            handler: this.deleteRule.bind(this, record)
        }));
        menu.showAt(e.pageX, e.pageY);
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openRule(record);
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
                    url: '/plugin/CoreShop/admin_PriceRules/list',
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
                            text: t("coreshop_price_rule_add"),
                            iconCls: "pimcore_icon_add",
                            handler: this.addRule.bind(this)
                        }, {
                            // spacer
                            xtype: 'tbfill'
                        }, {
                            // save button
                            id: 'btnSave',
                            hidden: true,
                            text: t("coreshop_price_rule_save_order"),
                            iconCls: "pimcore_icon_save",
                            handler: function() {
                                // this
                                var button = this;

                                // get current order
                                var prio = 0;
                                var rules = {};

                                this.ownerCt.ownerCt.getRootNode().eachChild(function (rule){
                                    prio++;
                                    rules[ rule.id ] = prio;
                                });

                                // save order
                                Ext.Ajax.request({
                                    url: "/plugin/CoreShop/admin_PriceRules/save-order",
                                    params: {
                                        rules: Ext.encode(rules)
                                    },
                                    method: "post",
                                    success: function(){
                                        button.hide();
                                    }
                                });

                            }
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
    addRule: function () {
        Ext.MessageBox.prompt(t('coreshop_price_rule_config_add'), t('coreshop_price_rule_enter_the_name_of_the_new_rule'),
            this.addRuleComplete.bind(this), null, null, "");
    },


    /**
     * save added item
     * @param button
     * @param value
     * @param object
     * @todo ...
     */
    addRuleComplete: function (button, value, object) {

        var regresult = value.match(/[a-zA-Z0-9_\-]+/);
        if (button == "ok" && value.length > 2 && regresult == value) {
            Ext.Ajax.request({
                url: "/plugin/CoreShop/admin_PriceRules/add",
                params: {
                    name: value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.tree.getStore().load();

                    if(!data || !data.success) {
                        Ext.Msg.alert(t('add_target'), t('problem_creating_new_target'));
                    } else {
                        this.openRule(intval(data.id));
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
     * delete existing rule
     */
    deleteRule: function (record) {
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_PriceRules/delete",
            params: {
                id: record.get("id")
            },
            success: function () {
                this.store.load();
            }.bind(this)
        });
    },


    /**
     * open pricing rule
     * @param node
     */
    openRule: function (node) {

        if(!is_numeric(node)) {
            node = node.id;
        }

        var priceRulePanelKey = "coreshop_price_rule" + node;

        if(this.panels[priceRulePanelKey]) {
            this.panels[priceRulePanelKey].activate();
        } else {

            // load defined rules
            Ext.Ajax.request({
                url: "/plugin/CoreShop/admin_PriceRules/get",
                params: {
                    id: node
                },
                success: function (response) {
                    var res = Ext.decode(response.responseText);
                    var item = new pimcore.plugin.coreshop.pricerule.item(this, res);

                    this.panels[priceRulePanelKey] = item;
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
