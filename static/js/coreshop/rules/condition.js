/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.condition');

pimcore.plugin.coreshop.rules.condition = Class.create({
    initialize : function (conditions) {
        this.conditions = conditions;
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined conditions
        Ext.each(this.conditions, function (condition) {

            if (condition == 'abstract')
                return;

            addMenu.push({
                iconCls: 'coreshop_rule_icon_condition_' + condition,
                text: t('coreshop_condition_' + condition),
                handler: _this.addCondition.bind(_this, condition, null)
            });

        });

        this.conditionsContainer = new Ext.Panel({
            iconCls: 'coreshop_rule_conditions',
            title: t('conditions'),
            autoScroll: true,
            style : 'padding: 10px',
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.conditionsContainer;
    },

    getConditionClassItem : function(type) {
        return pimcore.plugin.coreshop.rules.conditions[type];
    },

    addCondition: function (type, data) {
        // create condition
        var conditionClass = this.getConditionClassItem(type);
        var item = new conditionClass(this, data);

        // add logic for brackets
        var tab = this;

        this.conditionsContainer.add(item.getLayout());
        this.conditionsContainer.updateLayout();
    },

    getConditionsData : function () {
        // get defined conditions
        var conditionsData = [];
        var conditions = this.conditionsContainer.items.getRange();
        for (var i = 0; i < conditions.length; i++) {
            var condition = {};

            var conditionItem = conditions[i];
            var conditionClass = conditionItem.xparent;

            if(Ext.isFunction(conditionClass['getValues'])) {
                condition = conditionClass.getValues();
            }
            else {
                var form = conditionClass.form;

                for (var c = 0; c < form.items.length; c++) {
                    var item = form.items.get(c);

                    try {
                        condition[item.getName()] = item.getValue();
                    }
                    catch (e) {

                    }

                }
            }

            condition['type'] = conditions[i].xparent.type;
            conditionsData.push(condition);
        }

        return conditionsData;
    }
});
