/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('coreshop.product.unit.builder');
coreshop.product.unit.builder = Class.create({

    data: null,
    fieldConfig: null,
    objectId: null,
    form: null,
    unitStore: null,
    additionalUnitsCounter: 0,
    unitStoresInitialized: false,
    dirty: false,

    initialize: function (unitStore, fieldConfig, data, objectId) {

        this.additionalUnitsCounter = 0;
        this.fieldConfig = fieldConfig;
        this.data = data;
        this.objectId = objectId;
        this.dirty = false;
        this.unitStore = unitStore;

        this.setupForm();

    },

    getForm: function () {
        return this.form;
    },

    setupForm: function () {
        this.form = new Ext.form.Panel({
            closable: false,
            style: 'padding: 10px;'
        });

        this.form.add(this.getUnitForm());
    },

    getUnitForm: function () {

        // do not show extra unit fields if no units are available.
        if (this.unitStore.getRange().length === 0) {
            return [];
        }

        return [
            this.getDefaultUnitDefinitionField(),
            this.getAdditionalUnitDefinitionsField()
        ]
    },

    getDataValue: function (key) {

        var data;

        data = this.data !== null && Ext.isObject(this.data) ? this.data : null;
        if (data === null) {
            return null;
        }

        if (data.hasOwnProperty(key)) {
            return data[key];
        }

        return null;
    },

    getDefaultUnitDefinitionField: function () {

        var defaultUnitDefinition = this.getDefaultUnitDefinition(),
            hasId = defaultUnitDefinition !== null && defaultUnitDefinition.hasOwnProperty('id') && defaultUnitDefinition.id !== null,
            unitFieldForm = this.getUnitFormFields({
                idName: 'defaultUnitDefinition.id',
                idValue: hasId ? defaultUnitDefinition.id : null,
                unitName: 'defaultUnitDefinition.unit',
                unitLabel: 'coreshop_product_unit_default_type',
                unitValue: defaultUnitDefinition !== null ? defaultUnitDefinition.unit.id : this.getDefaultUnitStoreValue(),
                precisionLabel: 'coreshop_product_unit_precision',
                precisionName: 'defaultUnitDefinition.precision',
                precisionValue: defaultUnitDefinition !== null && !isNaN(defaultUnitDefinition.precision) ? defaultUnitDefinition.precision : 0,
            }, true);

        return Ext.create('Ext.form.Panel', {
            width: 950,
            items: [
                {
                    xtype: 'fieldset',
                    title: t('coreshop_product_unit_default_unit_definition_headline'),
                    layout: 'hbox',
                    itemCls: 'object_field',
                    items: unitFieldForm
                }
            ]
        });
    },

    getAdditionalUnitDefinitionsField: function () {

        var additionalUnitData = this.getAdditionalUnitDefinitions(), fieldSet;

        fieldSet = new Ext.form.FieldSet({
            title: t('coreshop_product_unit_additional_unit_definitions_headline'),
            collapsible: false,
            autoHeight: true,
            width: 950,
            style: 'margin-top: 20px;',
            itemId: 'additional-units-fieldset',
            listeners: {
                afterrender: function () {
                    this.checkAddUnitBlockAvailability(fieldSet);
                    this.adjustUnitStores(true);
                    this.adjustAdditionalUnitLabel();
                }.bind(this)
            },
            items: this.unitStore.getRange().length === 1 ? [] : [{
                xtype: 'toolbar',
                style: 'margin-bottom: 10px; padding: 5px;',
                height: 50,
                items: ['->', {
                    xtype: 'button',
                    iconCls: 'pimcore_icon_add',
                    itemId: 'additional-unit-add-button',
                    handler: function (b) {
                        var fieldSet = b.up('fieldset');
                        this.addAdditionalUnitField(fieldSet, null);
                        this.checkAddUnitBlockAvailability(fieldSet);
                        this.adjustUnitStores(false);
                        this.adjustAdditionalUnitLabel();
                        this.dispatchUnitDefinitionChangeEvent();
                    }.bind(this)
                }]
            }]
        });

        if (this.unitStore.getRange().length === 1) {
            fieldSet.add([{
                'xtype': 'label',
                'style': 'margin:5px; font-style:italic;',
                'html': t('coreshop_product_unit_no_additional_unit_definitions_available')
            }]);
        } else {
            Ext.Array.each(additionalUnitData, function (unit) {
                this.addAdditionalUnitField(fieldSet, unit);
            }.bind(this));
        }


        return fieldSet;
    },

    getDefaultUnitDefinition: function () {
        return this.getDataValue('defaultUnitDefinition');
    },

    getAdditionalUnitDefinitions: function () {
        var defaultUnitDefinition = this.getDefaultUnitDefinition(),
            unitDefinitions = this.getDataValue('unitDefinitions'),
            additionalUnits = [];

        if (unitDefinitions === null || !Ext.isArray(unitDefinitions)) {
            return [];
        }

        Ext.Array.each(unitDefinitions, function (unitDefinition) {
            if (unitDefinition.hasOwnProperty('unit') && unitDefinition.unit.id !== defaultUnitDefinition.unit.id) {
                additionalUnits.push(unitDefinition);
            }
        });

        return additionalUnits;
    },

    addAdditionalUnitField: function (fieldSet, data) {

        this.additionalUnitsCounter++;

        var hasId = data !== null && data.hasOwnProperty('id') && data.id !== null,
            compositeField,
            unitFieldForm = this.getUnitFormFields({
                idName: 'additionalUnitDefinitions.' + this.additionalUnitsCounter + '.id',
                idValue: hasId ? data.id : null,
                unitName: 'additionalUnitDefinitions.' + this.additionalUnitsCounter + '.unit',
                unitLabel: 'coreshop_product_unit_type',
                unitValue: data !== null && Ext.isObject(data) ? data.unit.id : null,
                conversionRateName: 'additionalUnitDefinitions.' + this.additionalUnitsCounter + '.conversionRate',
                precisionLabel: 'coreshop_product_unit_precision',
                precisionName: 'additionalUnitDefinitions.' + this.additionalUnitsCounter + '.precision',
                precisionValue: data !== null && !isNaN(data.precision) ? data.precision : 0,
                conversionRateLabel: 'coreshop_product_unit_conversion_rate',
                conversionRateValue: data !== null ? data.conversionRate : 0
            }, false);

        compositeField = new Ext.form.FieldContainer({
            layout: 'hbox',
            hideLabel: true,
            itemCls: 'object_field additional-unit-field-container',
            items: unitFieldForm
        });

        compositeField.add({xtype: 'tbfill'});
        compositeField.add({
            xtype: 'button',
            itemId: 'additional-unit-delete-button',
            iconCls: 'pimcore_icon_delete',
            cls: 'coreshop-transparent-btn',
            handler: this.onAdditionalUnitDelete.bind(this, fieldSet, compositeField)
        });

        fieldSet.add(compositeField);
    },

    onAdditionalUnitDelete: function (fieldSet, compositeField) {

        Ext.MessageBox.confirm(
            t('info'),
            t('coreshop_product_unit_additional_unit_definition_delete_confirm'),
            function (buttonValue) {

                if (buttonValue !== 'yes') {
                    return;
                }

                fieldSet.remove(compositeField);

                this.dirty = true;
                this.checkAddUnitBlockAvailability(fieldSet);
                this.adjustUnitStores(false);
                this.dispatchUnitDefinitionChangeEvent();

            }.bind(this));
    },

    adjustUnitStores: function (initializing) {

        var recheck = false,
            combos,
            additionalUnitCombos = this.form.query('combo[itemCls~=unit-store][cls!=default-unit-store]'),
            defaultUnitDefinitionCombo = this.form.query('combo[cls~=default-unit-store]');

        // default unit store needs to be last!
        combos = Ext.Array.merge(additionalUnitCombos, defaultUnitDefinitionCombo);

        Ext.Array.each(combos, function (combo) {

            var disallowed = [], clonedStore;

            Ext.Array.each(combos, function (subCombo) {
                if (combo.getName() !== subCombo.getName()) {
                    if (subCombo.getValue() === null) {
                        recheck = true;
                    }
                    disallowed.push(subCombo.getValue());
                }
            }.bind(this));

            if (combo.readOnly === true) {
                combo.setStore(this.unitStore);
            } else {
                clonedStore = this.cloneStore(this.unitStore, disallowed);
                combo.setStore(clonedStore);
                // current combo value is not allowed anymore
                if (disallowed.indexOf(combo.getValue()) !== -1 || combo.getValue() === null) {
                    combo.suspendEvents();
                    combo.setValue(clonedStore.first());
                    combo.resumeEvents(true);
                }
            }

        }.bind(this));

        if (initializing === true) {
            this.unitStoresInitialized = true;
        }

        if (recheck === true) {
            this.adjustUnitStores(false);
        }
    },

    dispatchUnitDefinitionChangeEvent: function () {

        var values = this.convertDotNotationToObject(this.getValues()),
            additionalUnitDefinitions = this.getAdditionalUnitDefinitions();

        if (values.hasOwnProperty('additionalUnitDefinitions')) {
            Ext.Object.each(values.additionalUnitDefinitions, function (index, additionalUnitDefinition) {
                var unitId = null, id = null;
                if (additionalUnitDefinition.hasOwnProperty('unit')) {
                    unitId = additionalUnitDefinition.unit;
                }

                if (Ext.isArray(additionalUnitDefinitions)) {
                    Ext.Array.each(additionalUnitDefinitions, function (additionalUnitDefinition) {
                        if (additionalUnitDefinition.hasOwnProperty('id')
                            && additionalUnitDefinition.hasOwnProperty('unit')
                            && Ext.isObject(additionalUnitDefinition.unit)
                            && additionalUnitDefinition.unit.id === unitId) {
                            id = additionalUnitDefinition.id;
                        }

                    }.bind(this));
                }

                if (id !== null) {
                    values.additionalUnitDefinitions[index].id = id;
                }
            });
        }

        coreshop.broker.fireEvent(
            'pimcore.object.tags.coreShopProductUnitDefinitions.change',
            {
                objectId: this.objectId,
                availableUnitDefinitions: values
            }
        );
    },

    adjustAdditionalUnitLabel: function () {

        var unitData,
            labelText,
            defaultUnitDefinitionStore = this.form.query('combo[cls~=default-unit-store]')[0],
            additionalUnitLabels = this.form.getComponent('additional-units-fieldset').query('label[itemCls~=conversion-rate-label]'),
            defaultUnitDefinitionStoreValue = defaultUnitDefinitionStore.getValue();

        if (!defaultUnitDefinitionStoreValue) {
            labelText = '--';
        } else {
            unitData = this.unitStore.getById(defaultUnitDefinitionStoreValue);
            labelText = unitData.get('fullLabel') ? unitData.get('fullLabel') : unitData.get('name');
        }

        Ext.Array.each(additionalUnitLabels, function (additionalUnitLabel) {
            additionalUnitLabel.setText(labelText);
        });
    },

    checkAddUnitBlockAvailability: function (comp) {
        var unitDefinitions = comp.query('fieldcontainer'),
            addButton = comp.query('button[itemId="additional-unit-add-button"]')[0];

        // no additional units available.
        if (addButton === undefined) {
            return;
        }

        // -1 = default unit store cannot be selected
        addButton.setVisible(unitDefinitions.length < this.unitStore.getRange().length - 1);
    },

    cloneStore: function (store, disallowed) {
        var records = [];
        store.each(function (r) {
            if (disallowed.indexOf(r.get('id')) === -1) {
                records.push(r.copy());
            }
        });

        var store2 = new Ext.data.Store({
            recordType: store.recordType
        });

        store2.add(records);

        return store2;
    },

    getDefaultUnitStoreValue: function () {
        if (this.unitStore.isLoaded()) {
            return this.unitStore.first().get('id');
        }

        return null;
    },

    getUnitFormFields: function (data, isDefault) {

        var fields = [
            {
                xtype: 'combo',
                fieldLabel: t(data.unitLabel),
                name: data.unitName,
                labelWidth: isDefault ? 120 : 80,
                store: null,
                triggerAction: 'all',
                itemCls: 'unit-store',
                cls: (isDefault ? 'default-unit-store' : ''),
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                displayField: 'fullLabel',
                valueField: 'id',
                value: data.unitValue,
                maxWidth: isDefault ? 280 : 200,
                readOnly: data.idValue !== null && isDefault === false,
                listeners: {
                    beforerender: this.onBeforeUnitComboRender.bind(this, data),
                    change: function (comp, value) {
                        if (comp.getName() === 'defaultUnitDefinition.unit' && value) {
                            this.adjustAdditionalUnitLabel();
                        }
                        if (this.unitStoresInitialized === true) {
                            this.adjustUnitStores();
                            this.dispatchUnitDefinitionChangeEvent();
                        }

                    }.bind(this)
                }
            }
        ];

        fields.push({
            xtype: 'numberfield',
            fieldLabel: t(data.precisionLabel),
            name: data.precisionName,
            labelWidth: 80,
            minValue: 0,
            value: data.precisionValue,
            decimalPrecision: 1,
            maxWidth: 150,
        });

        if (isDefault === false) {

            fields.push({
                xtype: 'numberfield',
                fieldLabel: t(data.conversionRateLabel),
                name: data.conversionRateName,
                labelWidth: 140,
                minValue: 0,
                value: data.conversionRateValue,
                decimalPrecision: 2,
                maxWidth: 250,
            });

            fields.push({
                xtype: 'label',
                text: 'item',
                itemCls: 'conversion-rate-label',
                style: 'margin: 7px 0 0 -4px;'
            });
        }

        return fields;

    },

    onBeforeUnitComboRender: function (data, comp) {
        // keep it for 3rd party modifiers.
    },

    postSaveObject: function (object, refreshedData) {

        if (Ext.isObject(refreshedData)) {
            this.data = refreshedData;
        }

        this.dirty = false;

        this.form.getForm().getFields().each(function (item) {
            item.resetOriginalValue();
        });
    },

    isDirty: function () {

        if (this.dirty === true) {
            return true;
        }

        if (this.form.getForm().isDirty()) {
            return true;
        }

        return false;
    },

    getValues: function () {
        return this.form.getForm().getFieldValues();
    },

    convertDotNotationToObject: function (data) {
        var obj = {};

        Object.keys(data).forEach(function (key) {
            var val = data[key],
                step = obj;

            key.split('.').forEach(function (part, index, arr) {
                if (index === arr.length - 1) {
                    step[part] = val;
                } else if (step[part] === undefined) {
                    step[part] = {};
                }
                step = step[part];
            });
        });

        return obj;
    }
});
