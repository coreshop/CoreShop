/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.object.objectMultihref');
coreshop.object.objectMultihref = Class.create(pimcore.object.tags.manyToManyObjectRelation, {

    type: 'objectMultihref',
    dataChanged: false,

    initialize: function (data, fieldConfig) {
        this.data = [];
        this.fieldConfig = fieldConfig;

        if (data) {
            this.data = data;
        }

        this.store = new Ext.data.ArrayStore({
            listeners: {
                add: function () {
                    this.dataChanged = true;
                }.bind(this),
                remove: function () {
                    this.dataChanged = true;
                }.bind(this),
                clear: function () {
                    this.dataChanged = true;
                }.bind(this),
                update: function (store) {
                    this.dataChanged = true;
                }.bind(this)
            },
            fields: ['id'],
            expandData: true
        });

        this.store.loadData(this.data);
    },

    createLayout: function (readOnly) {
        var autoHeight = false;
        if (intval(this.fieldConfig.height) < 15) {
            autoHeight = true;
        }

        var cls = 'object_field';

        var columns = [
            {
                header: 'ID',
                dataIndex: 'id',
                width: 50
            },
            {
                header: t("reference"),
                dataIndex: 'path',
                flex: 1,
                sortable: false
            },
            {
                xtype: 'actioncolumn',
                width: 40,
                items: [
                    {
                        tooltip: t('open'),
                        iconCls: 'coreshop_icon_cursor',
                        handler: function (grid, rowIndex) {
                            var data = grid.getStore().getAt(rowIndex);
                            pimcore.helpers.openObject(data.data.id, 'object');
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                width: 40,
                items: [
                    {
                        tooltip: t('remove'),
                        iconCls: 'pimcore_icon_delete',
                        handler: function (grid, rowIndex) {
                            grid.getStore().removeAt(rowIndex);
                        }.bind(this)
                    }
                ]
            }
        ];

        this.component = new Ext.grid.GridPanel({
            store: this.store,
            selModel: Ext.create('Ext.selection.RowModel', {}),
            minHeight: 150,
            border: true,
            viewConfig: {
                forceFit: true
            },
            columns: columns,
            cls: cls,

            //autoExpandColumn: 'path',
            width: this.fieldConfig.width,
            height: this.fieldConfig.height,
            tbar: {
                items: [
                    {
                        xtype: 'tbspacer',
                        width: 20,
                        height: 16,
                        cls: 'pimcore_icon_droptarget'
                    },
                    {
                        xtype: 'tbtext',
                        text: '<b>' + this.fieldConfig.title + '</b>'
                    },
                    '->',
                    {
                        xtype: 'button',
                        iconCls: 'pimcore_icon_search',
                        handler: this.openSearchEditor.bind(this)
                    },
                    {
                        xtype: 'button',
                        iconCls: 'pimcore_icon_delete',
                        handler: this.empty.bind(this)
                    }
                ],
                ctCls: 'pimcore_force_auto_width',
                cls: 'pimcore_force_auto_width'
            },
            autoHeight: autoHeight,
            bodyCssClass: 'pimcore_object_tag_objects'
        });

        this.component.on('rowcontextmenu', this.onRowContextmenu);
        this.component.reference = this;

        if (!readOnly) {
            this.component.on('afterrender', function () {

                var dropTargetEl = this.component.getEl();
                var gridDropTarget = new Ext.dd.DropZone(dropTargetEl, {
                    ddGroup: 'element',
                    getTargetFromEvent: function (e) {
                        return this.component.getEl().dom;
                    }.bind(this),
                    onNodeOver: function (overHtmlNode, ddSource, e, data) {
                        var fromTree = this.isFromTree(ddSource);

                        // Check if any of the records can be dropped
                        for (var record of data.records) {
                            var recordData = record.data;
                            if (recordData.elementType === 'object' && this.dndAllowed(recordData, fromTree)) {
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            }
                        }

                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                    }.bind(this),
                    onNodeDrop: function (target, ddSource, e, data) {
                        var fromTree = this.isFromTree(ddSource);

                        // check if data is a treenode, if not allow drop because of the reordering
                        if (!fromTree) {
                            return true;
                        }

                        var addedAny = false;

                        // Process all records in the drag selection
                        for (var record of data.records) {
                            var recordData = record.data;

                            if (recordData.elementType !== 'object') {
                                continue;
                            }

                            if (this.dndAllowed(recordData, fromTree)) {
                                var initData = {
                                    id: recordData.id,
                                    path: recordData.path,
                                    type: recordData.className
                                };

                                if (!this.objectAlreadyExists(initData.id)) {
                                    this.store.add(initData);
                                    addedAny = true;
                                }
                            }
                        }

                        return addedAny;
                    }.bind(this)
                });
            }.bind(this));
        }

        this.requestNicePathData(this.store.data);

        return this.component;
    },

    getLayoutEdit: function () {
        return this.createLayout(false);
    },

    getLayoutShow: function () {
        return this.createLayout(true);
    },

    openSearchEditor: function () {
        var allowedClasses;
        if (this.fieldConfig.classes != null && this.fieldConfig.classes.length > 0) {
            allowedClasses = [];
            for (var i = 0; i < this.fieldConfig.classes.length; i++) {
                allowedClasses.push(this.fieldConfig.classes[i].classes);
            }
        }

        pimcore.helpers.itemselector(true, this.addDataFromSelector.bind(this), {
            type: ['object'],
            subtype: {
                object: ['object', 'folder', 'variant']
            },
            specific: {
                classes: allowedClasses
            }
        });
    },

    getValue: function () {
        var tmData = [];

        var data = this.store.queryBy(function (record, id) {
            return true;
        });

        for (var i = 0; i < data.items.length; i++) {
            tmData.push(data.items[i].data.id);
        }

        return tmData;
    },

    requestNicePathData: function (targets) {
        var elementData = [];

        targets.each(function (record) {
            elementData.push({
                type: 'object',
                id: record.get("id")
            });
        }, this);

        coreshop.helpers.requestNicePathData(elementData, pimcore.helpers.getNicePathHandlerStore.bind(this, this.store, {}, this.component.getView()));
    }
});
