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

pimcore.registerNS('coreshop.rules.condition');

coreshop.rules.condition = Class.create({
    initialize: function (conditions) {
        this.conditions = conditions;
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
                handler: _this.addCondition.bind(_this, condition, null)
            });

        });

        this.conditionsContainer = new Ext.Panel({
            iconCls: 'coreshop_rule_conditions',
            title: t('conditions'),
            autoScroll: true,
            style: 'padding: 10px',
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.conditionsContainer;
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

    addCondition: function (type, data) {
        // create condition
        var conditionClass = this.getConditionClassItem(type);
        var item = new conditionClass(this, type, data);

        // add logic for brackets
        var tab = this;

        this.conditionsContainer.add(item.getLayout());
        this.conditionsContainer.updateLayout();
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

            if (conditionClass.data.id) {
                action['id'] = conditionClass.data.id;
            }

            condition['configuration'] = configuration;
            condition['type'] = conditions[i].xparent.type;

            if (Ext.isFunction(this.prepareCondition)) {
                condition = this.prepareCondition(condition);
            }

            conditionsData.push(condition);
        }

        return conditionsData;
    },

    isDirty: function () {
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
