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
                    dataUrl: "/plugin/CoreShop/admin_PriceRules/list",
                    requestMethod: "GET",
                    baseAttrs: {
                        listeners: {
                            click: this.openRule.bind(this),
                            contextmenu: function () {
                                this.select();

                                var menu = new Ext.menu.Menu();
                                menu.add(new Ext.menu.Item({
                                    text: t('delete'),
                                    iconCls: "pimcore_icon_delete",
                                    handler: this.attributes.reference.deleteRule.bind(this)
                                }));

                                menu.show(this.ui.getAnchor());
                            }
                        },
                        reference: this,
                        iconCls: "coreshop_price_rule_icon",
                        leaf: true
                    }
                }),
                rootVisible: false,
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
                },
                // DD sorting with save button
                enableDD: true,
                listeners: {
                    enddrag: function(tree, node, e){
                        tree.getTopToolbar().items.get('btnSave').show();
                    }
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

                    this.tree.getRootNode().reload();

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
    deleteRule: function () {
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_PriceRules/delete",
            params: {
                id: this.id
            },
            success: function () {
                this.attributes.reference.tree.getRootNode().reload();
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

        // load defined rules
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_PriceRules/get",
            params: {
                id: node
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                var item = new pimcore.plugin.coreshop.pricerule.item(this, res);
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
