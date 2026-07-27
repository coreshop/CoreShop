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

pimcore.registerNS('coreshop.notification.rule.condition');

coreshop.notification.rule.condition = Class.create(coreshop.rules.condition, {
    initialize: function (conditions, type) {
        this.conditions = conditions;
        this.type = type;
    },

    getConditionStyleClass: function (condition) {
        return 'coreshop_rule_icon_condition_' + condition;
    },

    getConditionClassNamespace: function () {
        return coreshop.notification.rule.conditions;
    },

    reload: function (conditions) {
        this.conditionsContainer.removeAll();

        Ext.each(conditions, function (condition) {
            var conditionType = condition.type.replace(this.type + '.', '');

            if (this.conditions.indexOf(conditionType) >= 0) {
                this.addCondition(conditionType, condition, false);
            }
        }.bind(this));
    },

    prepareCondition: function (condition) {
        condition['type'] = this.type + '.' + condition['type'];

        return condition;
    }
});
