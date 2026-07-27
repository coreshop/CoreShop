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

pimcore.registerNS('coreshop.notification.rule.action');

coreshop.notification.rule.action = Class.create(coreshop.rules.action, {
    initialize: function (actions, type) {
        this.actions = actions;
        this.type = type;
    },

    getActionClassNamespace: function () {
        return coreshop.notification.rule.actions;
    },

    reload: function (actions) {
        this.actionsContainer.removeAll();

        Ext.each(actions, function (action) {
            var actionType = action.type.replace(this.type + '.', '');

            if (this.actions.indexOf(actionType) >= 0) {
                this.addAction(actionType, action, false);
            }
        }.bind(this));
    },

    prepareAction: function (action) {
        action['type'] = this.type + '.' + action['type'];

        return action;
    }
});
