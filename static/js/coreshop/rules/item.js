/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.item');

pimcore.plugin.coreshop.rules.item = Class.create(pimcore.plugin.coreshop.abstract.item, {
    getActionContainerClass : function() {
        return pimcore.plugin.coreshop.rules.action;
    },

    getConditionContainerClass : function() {
        return pimcore.plugin.coreshop.rules.condition;
    },

    getItems : function () {
        var actionContainerClass = this.getActionContainerClass();
        var conditionContainerClass = this.getConditionContainerClass();

        this.actions = new actionContainerClass(this.parentPanel.actions);
        this.conditions = new conditionContainerClass(this.parentPanel.conditions);

        var items = [
            this.getSettings(),
            this.conditions.getLayout(),
            this.actions.getLayout()
        ];

        // add saved conditions
        if (this.data.conditions)
        {
            Ext.each(this.data.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition);
            }.bind(this));
        }

        // add saved actions
        if (this.data.actions)
        {
            Ext.each(this.data.actions, function (action) {
                this.actions.addAction(action.type, action);
            }.bind(this));
        }

        return items;
    },

    getSaveData : function () {
        var saveData = {};

        // general settings
        saveData['settings'] = this.settingsForm.getForm().getFieldValues();
        saveData['conditions'] = this.conditions.getConditionsData();
        saveData['actions'] = this.actions.getActionsData();

        return {
            data : Ext.encode(saveData)
        };
    }
});
