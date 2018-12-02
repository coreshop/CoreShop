/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.rules.item');

coreshop.rules.item = Class.create(coreshop.resource.item, {
    getActionContainerClass: function () {
        return coreshop.rules.action;
    },

    getConditionContainerClass: function () {
        return coreshop.rules.condition;
    },

    getItems: function () {
        var actionContainerClass = this.getActionContainerClass();
        var conditionContainerClass = this.getConditionContainerClass();

        this.actions = new actionContainerClass(this.parentPanel.getActions());
        this.conditions = new conditionContainerClass(this.parentPanel.getConditions());

        var items = [
            this.getSettings(),
            this.conditions.getLayout(),
            this.actions.getLayout()
        ];

        // add saved conditions
        if (this.data.conditions) {
            Ext.each(this.data.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition);
            }.bind(this));
        }

        // add saved actions
        if (this.data.actions) {
            Ext.each(this.data.actions, function (action) {
                this.actions.addAction(action.type, action);
            }.bind(this));
        }

        return items;
    },

    getSaveData: function () {
        saveData = this.settingsForm.getForm().getFieldValues();
        saveData['conditions'] = this.conditions.getConditionsData();
        saveData['actions'] = this.actions.getActionsData();

        return saveData;
    }
});
