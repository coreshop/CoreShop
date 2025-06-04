/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.index.fields');

coreshop.index.fields = Class.create({
    data: {},
    brickKeys: [],

    initialize: function (data, klass) {
        this.data = data;
        this.class = klass;
    },

    setClass: function(klass) {
        this.class = klass;
    },

    reload: function() {
        if (this.classDefinitionTreePanel) {
            this.classDefinitionTreePanel.destroy();
        }

        this.classDefinitionTreePanel = this.getClassTree(Routing.generate('coreshop_index_getClassDefinitionForFieldSelection'), this.class);

        this.configPanel.add(this.classDefinitionTreePanel);
    },

    getLayout: function () {
        var items = [
            this.getSelectionPanel()
        ];

        if (this.class) {
            items.push(this.getClassDefinitionTreePanel());
        }

        this.configPanel = new Ext.Panel({
            layout: 'border',
            items: items

        });

        return this.configPanel;
    },

    getData: function () {

        var columns = {};

        if (this.selectionPanel) {
            var allowedColumns = [
                'name', 'getter', 'getterConfig', 'interpreter', 'interpreterConfig', 'columnType', 'configuration', 'objectType', 'dataType'
            ];

            this.selectionPanel.getRootNode().eachChild(function (child) {
                var obj = {
                    type: child.data.objectType,
                    objectKey: child.data.key
                };

                Ext.Object.each(Ext.Object.merge(child.data, {}), function (key, value) {

                    if (key === 'configuration') {
                        var configuration = {};

                        Ext.Object.each(value, function (ckey, cvalue) {
                            if (cvalue) {
                                configuration[ckey] = cvalue;
                            }
                        });

                        value = configuration;

                        if (Object.keys(configuration).length === 0) {
                            return;
                        }
                    }

                    if (value && allowedColumns.indexOf(key) >= 0) {
                        obj[key] = value;
                    }
                });

                if (!obj.hasOwnProperty('getter') && obj.hasOwnProperty('getterConfig')) {
                    delete obj['getterConfig'];
                }

                if (!obj.hasOwnProperty('interpreter') && obj.hasOwnProperty('interpreterConfig')) {
                    delete obj['interpreterConfig'];
                }

                columns[obj.name] = obj;
            }.bind(this));
        }

        return columns;
    },

    getSelectionPanel: function () {
        if (!this.selectionPanel) {

            var childs = [];
            if (this.data.hasOwnProperty('columns')) {
                for (var i = 0; i < this.data.columns.length; i++) {
                    var nodeConf = this.data.columns[i];
                    var child = Ext.Object.merge(nodeConf,
                        {
                            text: nodeConf.name,
                            type: 'data',
                            leaf: true,
                            iconCls: 'pimcore_icon_' + nodeConf.dataType,
                            key: nodeConf.objectKey
                        }
                    );

                    childs.push(child);
                }
            }

            this.selectionPanel = new Ext.tree.TreePanel({
                bufferedRenderer: false,
                root: {
                    id: '0',
                    root: true,
                    text: t('coreshop_indexes_selected_fields'),
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: childs
                },

                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        ddGroup: 'columnconfigelement'
                    },
                    listeners: {
                        beforedrop: function (node, data, overModel, dropPosition, dropHandlers, eOpts) {
                            var target = overModel.getOwnerTree().getView();
                            var source = data.view;

                            if (target !== source) {
                                var record = data.records[0];
                                var copy = record.createNode(Ext.apply({}, record.data));

                                copy.id = Ext.id();

                                var element = this.getConfigElement(copy);

                                element.getConfigDialog(copy);

                                data.records = [copy]; // assign the copy as the new dropNode
                            }
                        }.bind(this),
                        options: {
                            target: this.selectionPanel
                        }
                    }
                },
                region: 'east',
                title: t('coreshop_indexes_selected_fields'),
                layout: 'fit',
                width: 428,
                split: true,
                autoScroll: true,
                listeners: {
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

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();

        tree.select();

        var menu = new Ext.menu.Menu();

        if (this.id != 0) {
            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: 'pimcore_icon_delete',
                handler: function (node) {
                    this.selectionPanel.getRootNode().removeChild(record, true);
                }.bind(this, record)
            }));
            menu.add(new Ext.menu.Item({
                text: t('edit'),
                iconCls: 'pimcore_icon_edit',
                handler: function (node) {
                    this.getConfigElement(record).getConfigDialog(record);
                }.bind(this, record)
            }));
        }

        menu.showAt(e.pageX, e.pageY);
    },

    getConfigElement: function (record) {
        return new coreshop.index.objecttype.abstract(this);
    },

    /*
     *       FIELD-TREE
     *
     **/
    getClassDefinitionTreePanel: function () {
        if (!this.classDefinitionTreePanel) {
            this.brickKeys = [];

            if (this.class) {
                this.classDefinitionTreePanel = this.getClassTree(Routing.generate('coreshop_index_getClassDefinitionForFieldSelection'), this.class);
            }
        }

        return this.classDefinitionTreePanel;
    },

    getClassTree: function (url, klass) {

        var tree = new Ext.tree.TreePanel({
            title: t('class_definitions'),
            region: 'center',

            //ddGroup: "columnconfigelement",
            autoScroll: true,
            rootVisible: false,
            root: {
                id: '0',
                root: true,
                text: t('base'),
                allowDrag: false,
                leaf: true,
                isTarget: true
            },
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    enableDrag: true,
                    enableDrop: false,
                    ddGroup: 'columnconfigelement'
                }
            }
        });

        Ext.Ajax.request({
            url: url,
            params: {
                class: klass
            },
            success: this.initLayoutFields.bind(this, tree)
        });

        tree.addListener('itemdblclick', function (tree, record, item, index, e, eOpts) {
            if (
                !record.data.root &&
                (
                    record.datatype &&
                    record.datatype !== 'layout'
                ) ||
                (
                    record.data.dataType &&
                    record.data.dataType !== 'localizedfields'
                )
            ) {
                var copy = Ext.apply({}, record.data);

                copy.id = Ext.id();

                if (this.selectionPanel && !this.selectionPanel.getRootNode().findChild('name', record.data.name)) {
                    var node = this.selectionPanel.getRootNode().appendChild(copy);

                    var element = this.getConfigElement(node);
                    element.getConfigDialog(node);
                }
            }
        }.bind(this));

        return tree;
    },

    initLayoutFields: function (tree, response) {
        var data = Ext.decode(response.responseText);

        var keys = Object.keys(data);
        for (var i = 0; i < keys.length; i++) {
            if (data[keys[i]]) {
                if (data[keys[i]].childs) {
                    var text = t(data[keys[i]].nodeLabel);

                    if (data[keys[i]].nodeType == 'objectbricks') {
                        text = t(data[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    if (data[keys[i]].nodeType == 'classificationstore') {
                        text = t(data[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    if (data[keys[i]].nodeType == 'fieldcollections') {
                        text = t(data[keys[i]].nodeLabel) + ' ' + t('columns');
                    }

                    var baseNode = {
                        type: 'layout',
                        allowDrag: false,
                        iconCls: 'pimcore_icon_' + data[keys[i]].nodeType,
                        text: text
                    };

                    baseNode = tree.getRootNode().appendChild(baseNode);
                    for (var j = 0; j < data[keys[i]].childs.length; j++) {
                        if (
                            (data[keys[i]].nodeType == 'objectbricks' ||
                                data[keys[i]].nodeType == 'fieldcollections') &&
                            data[keys[i]].childs[j].nodeLabel == 'localizedfields') {

                            var text = t(data[keys[i]].childs[j].nodeLabel);

                            var node = {
                                type: 'layout',
                                allowDrag: false,
                                iconCls: 'pimcore_icon_' + data[keys[i]].childs[j].nodeType,
                                text: text
                            };

                            node = tree.getRootNode().appendChild(node);

                            for (var k = 0; k < data[keys[i]].childs[j].childs.length; k++) {
                                var innerNode = this.addDataChild.call(node, data[keys[i]].childs[j].childs[k].fieldtype, data[keys[i]].childs[j].childs[k], data[keys[i]].nodeType, data[keys[i]].className);
                                node.appendChild(innerNode);
                            }
                        } else {
                            var node = this.addDataChild.call(baseNode, data[keys[i]].childs[j].fieldtype, data[keys[i]].childs[j], data[keys[i]].nodeType, data[keys[i]].className);
                        }

                        baseNode.appendChild(node);
                    }

                    if (data[keys[i]].nodeType == 'object') {
                        baseNode.expand();
                    } else {
                        baseNode.collapse();
                    }
                }
            }
        }
    },

    addDataChild: function (type, initData, objectType, className) {

        if (type != 'objectbricks' && !initData.invisible) {
            var isLeaf = true;
            var draggable = true;

            var key = initData.name;

            var newNode = Ext.Object.merge(initData, {
                text: key,
                objectKey: initData.name,
                key: initData.name,
                type: 'data',
                layout: initData,
                leaf: isLeaf,
                allowDrag: draggable,
                dataType: type,
                iconCls: 'pimcore_icon_' + type,
                expanded: true,
                objectType: objectType,
                className: className
            });

            newNode = this.appendChild(newNode);

            if (this.rendered) {
                this.expand();
            }

            return newNode;
        } else {
            return null;
        }

    }
});
