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

pimcore.registerNS('coreshop.rules.condition');
coreshop.rules.condition = Class.create({
    dirty: false,
    panel: false,

    initialize: function (conditions, panel) {
        this.conditions = conditions;
        this.dirty = false;
        this.panel = panel;
    },

    reload: function (conditions) {
        this.conditionsContainer.removeAll();

        Ext.each(conditions, function(condition) {
            this.addCondition(condition.type, condition, false);
        }.bind(this));
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined conditions
        Ext.each(this.conditions, function (condition) {

            if (condition === 'abstract')
                return;

            addMenu.push({
                iconCls: _this.getConditionStyleClass(condition),
                text: t('coreshop_condition_' + condition),
                handler: _this.addCondition.bind(_this, condition, null, true)
            });
        });

        const buttons = [];

        if (!this.panel || this.panel.isAllowed('edit')) {
            buttons.push({
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            });
        }

        this.conditionsContainer = new Ext.Panel({
            iconCls: 'coreshop_rule_conditions',
            title: t('conditions'),
            autoScroll: true,
            style: 'padding: 10px',
            forceLayout: true,
            tbar: buttons,
            border: false
        });

        return this.conditionsContainer;
    },

    setDirty: function(dirty) {
        this.dirty = dirty;
    },

    destroy: function () {
        if (this.conditionsContainer) {
            this.conditionsContainer.destroy();
        }
    },

    getConditionStyleClass: function (condition) {
        return 'coreshop_rule_icon_condition_' + condition;
    },

    getConditionClassItem: function (type) {
        if (Object.keys(this.getConditionClassNamespace()).indexOf(type) >= 0) {
            return this.getConditionClassNamespace()[type];
        }

        return this.getDefaultConditionClassItem();
    },

    getConditionClassNamespace: function () {
        return coreshop.rules.conditions;
    },

    getDefaultConditionClassItem: function () {
        return coreshop.rules.conditions.abstract;
    },

    addCondition: function (type, data, dirty) {
        // create condition
        var conditionClass = this.getConditionClassItem(type);
        var item = new conditionClass(this, type, data);

        const itemLayout = item.getLayout();

        if (this.panel && !this.panel.isAllowed('edit')) {
            itemLayout.disable();
        }

        this.conditionsContainer.add(itemLayout);
        this.conditionsContainer.updateLayout();

        if (dirty) {
            this.setDirty(true);
        }
    },

    getConditionsData: function () {
        // get defined conditions
        var conditionsData = [];
        var conditions = this.conditionsContainer.items.getRange();
        for (var i = 0; i < conditions.length; i++) {
            var condition = {};
            var configuration = {};

            var conditionItem = conditions[i];
            var conditionClass = conditionItem.xparent;

            if (Ext.isFunction(conditionClass['getValues'])) {
                configuration = conditionClass.getValues();
            } else {
                var form = conditionClass.form;

                if (form) {
                    if (Ext.isFunction(form.getValues)) {
                        configuration = form.getValues();
                    }
                    else {
                        for (var c = 0; c < form.items.length; c++) {
                            var item = form.items.get(c);

                            try {
                                configuration [item.getName()] = item.getValue();
                            }
                            catch (e) {

                            }
                        }
                    }
                }
            }

            if (conditionClass.id) {
                condition['id'] = conditionClass.id;
            }

            condition['configuration'] = configuration;
            condition['type'] = conditions[i].xparent.type;
            condition['sort'] = (i + 1);

            if (Ext.isFunction(this.prepareCondition)) {
                condition = this.prepareCondition(condition);
            }

            conditionsData.push(condition);
        }

        return conditionsData;
    },

    isDirty: function () {
        if (this.dirty) {
            return true;
        }

        if (this.conditionsContainer.items) {
            var conditions = this.conditionsContainer.items.getRange();
            for (var i = 0; i < conditions.length; i++) {
                var conditionItem = conditions[i];
                var conditionClass = conditionItem.xparent;

                if (Ext.isFunction(conditionClass['isDirty'])) {
                    if (conditionClass.isDirty()) {
                        return true;
                    }
                } else {
                    var form = conditionClass.form;

                    if (form && Ext.isFunction(form.isDirty)) {
                        if (form.isDirty()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
});
