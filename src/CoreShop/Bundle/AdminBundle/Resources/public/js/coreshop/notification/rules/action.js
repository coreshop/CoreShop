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

pimcore.registerNS('pimcore.plugin.coreshop.notification.rules.action');

pimcore.plugin.coreshop.notification.rules.action = Class.create(pimcore.plugin.coreshop.rules.action, {
    initialize: function (actions, type) {
        this.actions = actions;
        this.type = type;
    },

    getActionClassNamespace: function () {
        return pimcore.plugin.coreshop.notification.rules.actions;
    },

    prepareAction: function (action) {
        action['type'] = this.type + '.' + action['type'];

        return action;
    }
});
