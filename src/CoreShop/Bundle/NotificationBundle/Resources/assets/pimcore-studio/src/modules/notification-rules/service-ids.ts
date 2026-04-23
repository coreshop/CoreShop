/**
 * CoreShop NotificationBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export const coreshopNotificationServiceIds = {
  // Notification Rule Registries - one for each notification type
  notificationRuleConditionRegistry: Symbol.for('coreshop.notification.notification_rule.condition_registry'),
  notificationRuleActionRegistry: Symbol.for('coreshop.notification.notification_rule.action_registry')
}
