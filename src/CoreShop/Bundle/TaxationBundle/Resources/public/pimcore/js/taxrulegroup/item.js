/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.taxrulegroup.item');
coreshop.taxrulegroup.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_tax_rule_groups',

    routing: {
        save: 'coreshop_tax_rule_group_save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getFormPanel: function () {
        var data = this.data;

        var items = [
            {
                name: 'name',
                fieldLabel: t('name'),
                value: data.name
            },
            {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: data.active
            }
        ];

        this.formPanel = new Ext.form.Panel({
            bodyStyle: 'padding:20px 5px 20px 5px;',
            border: false,
            region: 'center',
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
                    xtype: 'fieldset',
                    autoHeight: true,
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: {width: 400},
                    items: items
                },
                this.getGrid()
            ]
        });

        return this.formPanel;
    },

    getGrid: function () {
        var listeners = {};

        var modelName = 'coreshop.model.taxrules';

        if (!Ext.ClassManager.get(modelName)) {
            Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'taxRuleGroup', 'tax', 'behavior']
                }
            );
        }

        this.store = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            model: modelName,
            listeners: listeners,
            data: this.data.taxRules
        });

        var taxRatesStore = Ext.create('store.coreshop_tax_rates');
        taxRatesStore.load();

        var gridColumns = [
            {
                header: t('coreshop_tax'),
                width: 200,
                dataIndex: 'taxRate',
                editor: new Ext.form.ComboBox({
                    store: taxRatesStore,
                    valueField: 'id',
                    displayField: 'name',
                    queryMode: 'local'
                }),
                renderer: function (taxRate) {
                    var record = taxRatesStore.getById(taxRate);

                    if (record) {
                        return record.get('name');
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
                    queryMode: 'local'
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
                xtype: 'actioncolumn',
                width: 40,
                tooltip: t('delete'),
                iconCls: 'pimcore_icon_delete',
                handler: function (grid, rowIndex) {
                    grid.getStore().removeAt(rowIndex);
                }.bind(this)
            }
        ];

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {

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
                            id: null,
                            taxRuleGroup: this.data.id,
                            tax: null,
                            behavior: 0
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

        return this.grid;
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();
        var taxRules = [];

        this.store.getRange().forEach(function (range, index) {
            var data = range.data;

            if (range.phantom) {
                delete data['id'];
            }

            taxRules.push(data);
        });

        if (!values['active']) {
            delete values['active'];
        }

        values['taxRules'] = taxRules;

        return values;
    }
});
