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

pimcore.registerNS('pimcore.plugin.coreshop.taxrulegroups.item');

pimcore.plugin.coreshop.taxrulegroups.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_tax_rule_groups',

    url : {
        save : '/plugin/CoreShop/admin_tax-rule-group/save'
    },

    getItems : function () {
        return [this.getFormPanel()];
    },

    getFormPanel : function ()
    {
        var data = this.data;

        this.formPanel = new Ext.form.Panel({
            bodyStyle:'padding:20px 5px 20px 5px;',
            border: false,
            region : 'center',
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            buttons: [
                {
                    text: t('save'),
                    handler: this.save.bind(this),
                    iconCls: 'pimcore_icon_apply'
                }
            ],
            items: [
                {
                    xtype:'fieldset',
                    autoHeight:true,
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: { width: '100%' },
                    items :[
                        {
                            name: 'name',
                            fieldLabel: t('name'),
                            width: 400,
                            value: data.name
                        },
                        {
                            xtype: 'checkbox',
                            name: 'active',
                            fieldLabel: t('coreshop_tax_rule_group_active'),
                            width: 250,
                            checked: data.active
                        }
                    ]
                },
                this.getGrid()
            ]
        });

        return this.formPanel;
    },

    getGrid : function () {
        var listeners = {};

        var modelName = 'coreshop.model.taxrules';

        if (!Ext.ClassManager.get(modelName)) {
            Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'taxRuleGroupId', 'countryId', 'taxId', 'behavior']
                }
            );
        }

        this.store = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            remoteSort: true,
            model : modelName,
            listeners: listeners,
            proxy: {
                type: 'ajax',
                url: '/plugin/CoreShop/admin_tax-rule-group/list-rules',
                reader: {
                    type: 'json',
                    rootProperty : 'data'
                },
                extraParams : {
                    id : this.data.id
                }
            }
        });

        var statesStore = new Ext.data.Store({
            restful:    false,
            proxy:       new Ext.data.HttpProxy({
                url : '/plugin/CoreShop/admin_State/country'
            }),
            reader:     new Ext.data.JsonReader({

            }, [
                { name:'id' },
                { name:'name' }
            ]),
            listeners: {
                load: function (store) {
                    var rec = { id: 0, name: t('coreshop_all') };
                    store.insert(0, rec);
                }
            }
        });

        var stateEditor = new Ext.form.ComboBox({
            store: statesStore,
            valueField: 'id',
            displayField: 'name',
            queryMode : 'local',
            disabled : true
        });

        var gridColumns = [
            {
                header: t('coreshop_tax_rule_country'),
                width: 200,
                dataIndex: 'countryId',
                editor: new Ext.form.ComboBox({
                    store: pimcore.globalmanager.get('coreshop_countries'),
                    valueField: 'id',
                    displayField: 'name',
                    queryMode : 'local',
                    listeners : {
                        change : function (cmb, newValue) {
                            stateEditor.enable();
                            stateEditor.getStore().load({
                                params : {
                                    countryId : newValue
                                }
                            });
                        }
                    }
                }),
                renderer: function (countryId) {
                    var store = pimcore.globalmanager.get('coreshop_countries');
                    var pos = store.findExact('id', countryId);
                    if (pos >= 0) {
                        return store.getAt(pos).get('name');
                    }

                    return null;
                }
            },
            {
                header: t('coreshop_tax_rule_state'),
                width: 200,
                dataIndex: 'stateId',
                editor: stateEditor,
                renderer: function (stateId) {
                    var store = pimcore.globalmanager.get('coreshop_states');
                    var pos = store.findExact('id', stateId);
                    if (pos >= 0) {
                        return store.getAt(pos).get('name');
                    }

                    return t('coreshop_all');
                }
            },
            {
                header: t('coreshop_tax_rule_tax'),
                width: 200,
                dataIndex: 'taxId',
                editor: new Ext.form.ComboBox({
                    store: pimcore.globalmanager.get('coreshop_taxes'),
                    valueField: 'id',
                    displayField: 'name',
                    queryMode : 'local'
                }),
                renderer: function (countryId) {
                    var store = pimcore.globalmanager.get('coreshop_taxes');
                    var pos = store.findExact('id', countryId);
                    if (pos >= 0) {
                        return store.getAt(pos).get('name');
                    }

                    return null;
                }
            },
            {
                header: t('coreshop_tax_rule_behavior'),
                width: 300,
                dataIndex: 'behavior',
                editor: new Ext.form.ComboBox({
                    store: [[0, t('coreshop_tax_rule_behavior_disable')], [1, t('coreshop_tax_rule_behavior_combine')], [2, t('coreshop_tax_rule_behavior_on_after_another')]],
                    triggerAction: 'all',
                    editable: false,
                    queryMode : 'local'
                }),
                renderer: function (behavior) {
                    switch (parseInt(behavior)) {
                        case 0:
                            return t('coreshop_tax_rule_behavior_disable');
                            break;

                        case 1:
                            return t('coreshop_tax_rule_behavior_combine');
                            break;

                        case 2:
                            return t('coreshop_tax_rule_behavior_on_after_another');
                            break;
                    }
                }
            },
            {
                xtype:'actioncolumn',
                width:40,
                tooltip:t('delete'),
                icon:'/pimcore/static6/img/icon/cross.png',
                handler:function (grid, rowIndex) {
                    grid.getStore().removeAt(rowIndex);
                }.bind(this)
            }
        ];

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                beforeedit : function (editor, context) {
                    if (context.record) {
                        if (context.record.get('countryId')) {
                            stateEditor.enable();
                            stateEditor.getStore().load({
                                params : {
                                    countryId : context.record.get('countryId')
                                }
                            });
                        }
                    }
                }
            }
        });

        var gridConfig = {
            frame: false,
            store: this.store,
            border: true,
            columns: gridColumns,
            loadMask: true,
            columnLines: true,
            stripeRows: true,
            trackMouseOver: true,
            viewConfig: {
                forceFit: false
            },
            selModel: Ext.create('Ext.selection.RowModel', {}),
            tbar: [
                {
                    text: t('add'),
                    handler: function () {
                        this.store.add({
                            id : null,
                            taxRuleGroupId : this.data.id,
                            countryId : null,
                            taxId : null,
                            behavior : 0
                        });
                    }.bind(this),
                    iconCls: 'pimcore_icon_add'
                }
            ],
            plugins: [
                this.cellEditing
            ]
        };

        this.grid = Ext.create('Ext.grid.Panel', gridConfig);

        this.store.load();

        return this.grid;
    },

    getSaveData : function () {
        var values = this.formPanel.getForm().getFieldValues();
        var taxRules = [];

        this.store.getRange().forEach(function (range) {
            taxRules.push(range.data);
        });

        return {
            data : Ext.encode(values),
            taxRules : Ext.encode(taxRules)
        };
    },

    postSave: function () {
        this.store.load();
    }
});
