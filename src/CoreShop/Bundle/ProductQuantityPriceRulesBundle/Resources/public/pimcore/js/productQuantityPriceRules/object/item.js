/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product_quantity_price_rules.object');
pimcore.registerNS('coreshop.product_quantity_price_rules.object.item');
coreshop.product_quantity_price_rules.object.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_icon_price_rule',
    objectId: null,
    clipBoardDispatcherId: null,

    ranges: null,
    conditions: null,
    settings: null,

    postSaveObject: function (object, refreshedRuleData, task, fieldName) {
        // remove dirty flag!
        this.settings.getForm().setValues(this.settings.getForm().getValues());
        this.ranges.postSaveObject(object, refreshedRuleData, task, fieldName);
    },

    onClipboardUpdated: function () {
        this.ranges.onClipboardUpdated();
    },

    getPanel: function () {

        this.panel = new Ext.TabPanel({
            activeTab: 0,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            items: this.getItems(),
            tabConfig: {
                html: this.generatePanelTitle(this.data.name, this.data.active, this.data.priority)
            }
        });

        return this.panel;
    },

    generatePanelTitle: function (title, active, priority) {
        var data = [title];

        if (priority !== undefined) {
            data.push('<em>(Prio: ' + priority + ')</em>');
        }

        if (active === false) {
            data.push('<span class="pimcore_rule_disabled standalone"></span>')
        }

        return data.join(' ');
    },

    setObjectId: function (objectId) {
        this.objectId = objectId;
        this.initPanelAfterObjectIdHasSet();
    },

    initPanel: function () {
        // dont use it, we need the object id first!
    },

    initPanelAfterObjectIdHasSet: function () {
        this.panel = this.getPanel();
        this.parentPanel.getTabPanel().add(this.panel);
    },

    getRangeContainerClass: function () {
        return coreshop.product_quantity_price_rules.ranges;
    },

    getConditionContainerClass: function () {
        return coreshop.product_quantity_price_rules.condition;
    },

    getItems: function () {
        var rangeContainerClass = this.getRangeContainerClass();
        var conditionContainerClass = this.getConditionContainerClass();

        this.ranges = new rangeContainerClass(this.getId(), this.objectId, this.parentPanel.getClipboardManager(), this.parentPanel.getTranslatedStoreData('pricingBehaviourTypes'));
        this.conditions = new conditionContainerClass(this.parentPanel.getConditions());

        var items = [
            this.getSettings(),
            this.conditions.getLayout(),
            this.ranges.getLayout()
        ];

        // add saved conditions
        if (this.data.conditions) {
            Ext.each(this.data.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition, false);
            }.bind(this));
        }

        // create ranges grid builder
        this.ranges.addRanges(this.data.ranges ? this.data.ranges : {});

        return items;
    },

    getSettings: function () {
        this.settings = Ext.create('Ext.form.Panel', {
            trackResetOnLoad: true,
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [
                {
                    xtype: 'textfield',
                    name: 'name',
                    fieldLabel: t('name'),
                    width: 250,
                    value: this.data.name,
                    enableKeyEvents: true,
                    listeners: {
                        keyup: function (field) {
                            var activeField = field.up('form').getForm().findField('active'),
                                priorityField = field.up('form').getForm().findField('priority');
                            this.panel.setTitle(this.generatePanelTitle(field.getValue(), activeField.getValue(), priorityField.getValue()));
                        }.bind(this)
                    }
                },
                {
                    xtype: 'combobox',
                    name: 'calculationBehaviour',
                    fieldLabel: t('coreshop_product_quantity_price_rules_calculation_behaviour'),
                    width: 250,
                    editable: false,
                    value: this.data.calculationBehaviour,
                    store: this.parentPanel.getTranslatedStoreData('calculationBehaviourTypes')
                }, {
                    xtype: 'numberfield',
                    name: 'priority',
                    fieldLabel: t('coreshop_priority'),
                    value: this.data.priority ? this.data.priority : 0,
                    width: 250,
                    listeners: {
                        change: function (field) {
                            var nameField = field.up('form').getForm().findField('name'),
                                activeField = field.up('form').getForm().findField('active');
                            this.panel.setTitle(this.generatePanelTitle(nameField.getValue(), activeField.getValue(), field.getValue()));
                        }.bind(this)
                    }
                }, {
                    xtype: 'checkbox',
                    name: 'active',
                    fieldLabel: t('active'),
                    checked: this.data.active,
                    listeners: {
                        change: function (field, state) {
                            var nameField = field.up('form').getForm().findField('name'),
                                priorityField = field.up('form').getForm().findField('priority');
                            this.panel.setTitle(this.generatePanelTitle(nameField.getValue(), field.getValue(), priorityField.getValue()));
                        }.bind(this)
                    }
                }]
        });

        return this.settings;
    },

    hasId: function () {
        return this.data.id && this.data.id !== null;
    },

    setId: function (id) {
        this.data.id = id;
    },

    getId: function () {
        return this.data.id ? this.data.id : null;
    },

    resetDeepId: function () {
        this.setId(null);
        this.ranges.resetDeepId();
    },

    getSaveData: function () {

        var saveData;

        if (!this.settings.getForm()) {
            return {};
        }

        saveData = this.settings.getForm().getFieldValues();
        saveData['conditions'] = this.conditions.getConditionsData();
        saveData['ranges'] = this.ranges.getRangesData();

        if (this.hasId()) {
            saveData['id'] = this.getId();
        }

        return saveData;

    },

    isDirty: function () {

        if (this.settings.form.monitor && this.settings.getForm().isDirty()) {
            return true;
        }

        if (this.conditions.isDirty()) {
            return true;
        }

        if (this.ranges.isDirty()) {
            return true;
        }

        return false;
    }
});
