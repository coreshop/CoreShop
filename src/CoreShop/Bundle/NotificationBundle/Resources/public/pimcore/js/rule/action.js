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
