/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

pimcore.registerNS('pimcore.plugin.coreshop.notification.rules.condition');

pimcore.plugin.coreshop.notification.rules.condition = Class.create(pimcore.plugin.coreshop.rules.condition, {
    initialize : function (conditions, type) {
        this.conditions = conditions;
        this.type = type;
    },

    getConditionStyleClass: function(condition) {
        return 'coreshop_rule_icon_condition_' + condition;
    },

    getConditionClassNamespace : function() {
        return pimcore.plugin.coreshop.notification.rules.conditions;
    },

    prepareCondition : function(condition) {
        condition['type'] = this.type + '.' + condition['type'];

        return condition;
    }
});
