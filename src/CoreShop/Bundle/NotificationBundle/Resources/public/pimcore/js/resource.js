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

pimcore.registerNS('coreshop.notification.resource');
coreshop.notification.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_notification_rules', 'coreshop_notification_rule_list');
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
