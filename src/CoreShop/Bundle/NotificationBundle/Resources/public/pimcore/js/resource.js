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

pimcore.registerNS('coreshop.notification.resource');
coreshop.notification.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_notification_rules', 'coreshop/notification_rules');
        pimcore.globalmanager.get('coreshop_notification_rules').sort('sort', 'ASC');

        coreshop.broker.fireEvent('resource.register', 'coreshop.notification', this);
    },

    openResource: function (item) {
        if (item === 'notification_rule') {
            this.openNotificationRule();
        }
    },

    openNotificationRule: function () {
        try {
            pimcore.globalmanager.get('coreshop_notification_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_notification_rule_panel', new coreshop.notification.rule.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.notification.resource();
});