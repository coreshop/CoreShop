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
