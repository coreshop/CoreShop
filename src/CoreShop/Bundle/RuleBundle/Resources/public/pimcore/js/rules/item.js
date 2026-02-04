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
                this.conditions.addCondition(condition.type, condition, false);
            }.bind(this));
        }

        // add saved actions
        if (this.data.actions) {
            Ext.each(this.data.actions, function (action) {
                this.actions.addAction(action.type, action, false);
            }.bind(this));
        }

        return items;
    },

    resetDirty: function() {
        if (this.actions) {
            this.actions.setDirty(false);
        }

        if (this.conditions) {
            this.conditions.setDirty(false);
        }
    },

    postSave: function(result) {
        this.conditions.reload(result.data.conditions);
        this.actions.reload(result.data.actions);
    },

    getSaveData: function () {
        var saveData = this.settingsForm.getForm().getFieldValues();
        saveData['conditions'] = this.conditions.getConditionsData();
        saveData['actions'] = this.actions.getActionsData();

        return saveData;
    }
});
