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

pimcore.registerNS('coreshop.worker.type.mysql');
coreshop.index.worker.mysql = Class.create(coreshop.index.worker.abstract, {
    getFields: function (config) {
        var me = this;

        me.indexesGrid = me.getIndexGrid(false, config);
        me.localizedIndexesGrid = me.getIndexGrid(true, config);

        return {
            xtype: 'panel',
            items: [
                me.indexesGrid,
                me.localizedIndexesGrid
            ]
        };
    },

    getIndexGrid: function (localized, config) {
        var me = this;
        var modelName = 'coreshop.model.index.mysql';

        if (!Ext.ClassManager.get(modelName)) {
            Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    fields: ['type', 'columns']
                }
            );
        }

        var values = config[localized ? 'localizedIndexes' : 'indexes'];

        if (!Ext.isObject(values)) {
            values = {};
        }

        var store = Ext.create('Ext.data.Store', {
            // destroy the store if the grid is destroyed
            autoDestroy: true,
            proxy: {
                type: 'memory'
            },
            model: modelName,
            data: Object.values(values)
        });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                beforeedit: function (editor, context, eOpts) {
                    if (context.colIdx === 1) {
                        context.column.setEditor({
                            xtype: 'combo',
                            store: {
                                data: me.getColumns(localized)
                            },
                            mode: 'local',
                            displayField: 'name',
                            valueField: 'name',
                            forceSelection: true,
                            triggerAction: 'all',
                            allowBlank: false,
                            multiSelect: true
                        });

                    }
                }
            }
        });


        var grid = Ext.create({
            xtype: 'grid',
            store: store,
            minHeight: 200,
            title: t(('coreshop_index_mysql_index' + (localized ? '_localized' : ''))),
            columns: [{
                header: t('type'),
                dataIndex: 'type',
                width: 100,
                editor: {
                    xtype: 'combo',
                    store: [
                        ['INDEX', 'INDEX'],
                        ['UNIQUE', 'UNIQUE']
                    ]
                }
            }, {
                header: t('columns'),
                dataIndex: 'columns',
                flex: 1,
                editor: {
                    xtype: 'combo',
                    store: {
                        data: me.getColumns(localized)
                    },
                    mode: 'local',
                    displayField: 'name',
                    valueField: 'name',
                    forceSelection: true,
                    triggerAction: 'all',
                    allowBlank: false,
                    multiSelect: true
                }
            }],
            tbar: [{
                text: t('add'),
                iconCls: 'pimcore_icon_add',
                handler: function () {
                    rowEditing.cancelEdit();

                    // Create a model instance
                    var r = Ext.create(modelName, {
                        type: 'INDEX',
                        columns: ''
                    });

                    store.insert(0, r);
                    rowEditing.startEdit(r, 0);
                }
            }, {
                itemId: 'removeIndex',
                text: t('delete'),
                iconCls: 'pimcore_icon_delete',
                handler: function () {
                    var sm = grid.getSelectionModel();
                    rowEditing.cancelEdit();
                    store.remove(sm.getSelection());
                    if (store.getCount() > 0) {
                        sm.select(0);
                    }
                },
                disabled: true
            }],
            plugins: [rowEditing],
            listeners: {
                'selectionchange': function (view, records) {
                    grid.down('#removeIndex').setDisabled(!records.length);
                }
            }
        });

        return grid;
    },

    getColumns: function (localized) {
        var interpreters = this.parent.parentPanel.interpreterStore.getRange().filter(function (rec) {
            return rec.data.localized === true;
        }).map(function (rec) {
            return rec.getId();
        });
        var fields = Ext.Object.getValues(this.parent.fieldsPanel.getData());

        return fields.filter(function (field) {
            var result = false;

            if (field.objectType === 'localizedfield') {
                result = true;
            }
            else if (field.hasOwnProperty('interpreter') && interpreters.indexOf(field.interpreter) >= 0) {
                result = true;
            }

            return localized ? result : !result;
        });
    },

    getIndexData: function (localized) {
        var me = this,
            grid = localized ? me.localizedIndexesGrid : me.indexesGrid,
            availableFields = me.getColumns(localized).map(function (col) {
                return col.name;
            }),
            indexes = grid.getStore().getRange().map(function (rec) {
                return {
                    type: rec.data.type,
                    columns: rec.data.columns
                };
            }),
            indexesForServer = {};

        indexes.forEach(function (index) {
            index.columns = index.columns.filter(function (col) {
                return availableFields.indexOf(col) >= 0;
            });
            var cols = index.columns.join('');

            indexesForServer[cols] = index;
        });

        return indexesForServer;
    },

    getData: function () {
        return {
            indexes: this.getIndexData(false),
            localizedIndexes: this.getIndexData(true)
        };
    }
});
