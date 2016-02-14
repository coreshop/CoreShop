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

pimcore.registerNS("pimcore.plugin.coreshop.indexes.fields");

pimcore.plugin.coreshop.indexes.fields = Class.create({
    data: {},
    brickKeys: [],

    initialize: function (data) {
        this.data = data;
        this.config = data.config;
    },

    getLayout : function() {
        this.configPanel = new Ext.Panel({
            layout: "border",
            items: [this.getSelectionPanel(), this.getClassDefinitionTreePanel()]

        });

        return this.configPanel;
    },

    getData: function () {

        var data = {};
        if(this.languageField) {
            data.language = this.languageField.getValue();
        }

        if(this.selectionPanel) {
            data.columns = [];
            this.selectionPanel.getRootNode().eachChild(function(child) {
                var obj = {};
                obj.key = child.data.key;
                obj.label = child.data.text;
                obj.dataType = child.data.dataType;
                obj.config = child.data.config;

                if (child.data.width) {
                    obj.width = child.data.width;
                }

                data.columns.push(obj);
            }.bind(this));
        }

        return data;
    },

    getSelectionPanel: function () {
        if(!this.selectionPanel) {

            var childs = [];
            if(this.config.hasOwnProperty("columns")) {
                for (var i = 0; i < this.config.columns.length; i++) {
                    var nodeConf = this.config.columns[i];
                    var child = {
                        text: nodeConf.label,
                        key: nodeConf.key,
                        type: "data",
                        dataType: nodeConf.dataType,
                        leaf: true,
                        iconCls: "pimcore_icon_" + nodeConf.dataType,
                        config : nodeConf.config
                    };
                    if (nodeConf.width) {
                        child.width = nodeConf.width;
                    }
                    childs.push(child);
                }
            }

            this.selectionPanel = new Ext.tree.TreePanel({
                root: {
                    id: "0",
                    root: true,
                    text: t("coreshop_indexes_selected_fields"),
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: childs
                },

                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        ddGroup: "columnconfigelement"
                    },
                    listeners: {
                        beforedrop: function (node, data, overModel, dropPosition, dropHandlers, eOpts) {
                            var target = overModel.getOwnerTree().getView();
                            var source = data.view;

                            if (target != source) {
                                var record = data.records[0];

                                if (this.selectionPanel.getRootNode().findChild("key", record.data.key)) {
                                    dropHandlers.cancelDrop();
                                } else {
                                    var copy = record.createNode(Ext.apply({}, record.data));

                                    var element = this.getConfigElement(copy);
                                    element.getConfigDialog(copy);

                                    data.records = [copy]; // assign the copy as the new dropNode
                                }
                            }
                        }.bind(this),
                        options: {
                            target: this.selectionPanel
                        }
                    }
                },
                region:'east',
                title: t('coreshop_indexes_selected_fields'),
                layout:'fit',
                width: 428,
                split:true,
                autoScroll: true,
                listeners:{
                    itemcontextmenu: this.onTreeNodeContextmenu.bind(this)
                }
            });
            var store = this.selectionPanel.getStore();
            var model = store.getModel();
            model.setProxy({
                type: 'memory'
            });
        }

        return this.selectionPanel;
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        e.stopEvent();

        tree.select();

        var menu = new Ext.menu.Menu();

        if (this.id != 0) {
            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: "pimcore_icon_delete",
                handler: function(node) {
                    this.selectionPanel.getRootNode().removeChild(record, true);
                }.bind(this, record)
            }));
            menu.add(new Ext.menu.Item({
                text: t('edit'),
                iconCls: "pimcore_icon_edit",
                handler: function(node) {
                    this.getConfigElement(record).getConfigDialog(record);
                }.bind(this, record)
            }));
        }

        menu.showAt(e.pageX, e.pageY);
    },

    getConfigElement: function(record) {
        var element = null;

        if(record.data.type) {
            if(pimcore.plugin.coreshop.indexes.elements[this.data.type] && pimcore.plugin.coreshop.indexes.elements[this.data.type][record.data.type]) {
                //Check if there is an dialog for index-type and data-type
                element = new pimcore.plugin.coreshop.indexes.elements[this.data.type][record.data.type]();
            }
            else if (pimcore.plugin.coreshop.indexes.elements[this.data.type].default) {
                //Check if there is an default dialog for index-type
                element = new pimcore.plugin.coreshop.indexes.elements[this.data.type].default();
            }
        }

        return element;
    },

    getClassDefinitionTreePanel: function () {
        if (!this.classDefinitionTreePanel) {
            this.brickKeys = [];
            this.classDefinitionTreePanel = this.getClassTree("/admin/class/get-class-definition-for-column-config", this.data.classId);
        }

        return this.classDefinitionTreePanel;
    },

    getClassTree: function(url, classId) {

        var classTreeHelper = new pimcore.object.helpers.classTree(false);
        var tree = classTreeHelper.getClassTree(url, classId);

        tree.addListener("itemdblclick", function(tree, record, item, index, e, eOpts ) {
            if(!record.data.root && record.datatype != "layout"
                && record.data.dataType != 'localizedfields') {
                var copy = Ext.apply({}, record.data);

                if(this.selectionPanel && !this.selectionPanel.getRootNode().findChild("key", record.data.key)) {
                    this.selectionPanel.getRootNode().appendChild(copy);
                }

                if (record.data.dataType == "keyValue") {
                    var ccd = new pimcore.object.keyvalue.columnConfigDialog();
                    ccd.getConfigDialog(copy, this.selectionPanel);
                }
            }
        }.bind(this));

        return tree;
    }
});