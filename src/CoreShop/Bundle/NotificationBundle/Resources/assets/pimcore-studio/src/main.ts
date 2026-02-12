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

import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { NotificationBundleIconModule } from './modules/icon-library'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopNotificationServiceIds } from './modules/notification-rules/service-ids'
import { MailAction, StoreMailAction, OrderMailAction, StoreOrderMailAction } from './modules/notification-rules/actions'
import { NotificationRuleManager } from './modules/notification-rules/NotificationRuleManager'

const plugin: IAbstractPlugin = {
  name: 'coreshop-notification',

  onInit() {
    // ============================================
    // Notification Rules Registry Setup
    // ============================================
    // Create and bind registries for Notification Rules (SYNCHRONOUS!)
    container.bind(coreshopNotificationServiceIds.notificationRuleConditionRegistry)
      .to(ConditionRegistry)
      .inSingletonScope()

    container.bind(coreshopNotificationServiceIds.notificationRuleActionRegistry)
      .to(ActionRegistry)
      .inSingletonScope()

    // Get action registry
    const actionRegistry = container.get<ActionRegistry>(
      coreshopNotificationServiceIds.notificationRuleActionRegistry
    )

    // Register base mail action for all notification types
    // The action type is prefixed with the notification type by the backend
    // e.g., "order.mail", "payment.mail", etc.
    // We register a single "mail" action that works for all types
    actionRegistry.register('mail', MailAction)
    actionRegistry.register('storeMail', StoreMailAction)
    actionRegistry.register('orderMail', OrderMailAction)
    actionRegistry.register('storeOrderMail', StoreOrderMailAction)

    // Also register with type prefixes for compatibility
    const notificationTypes = ['order', 'payment', 'invoice', 'shipment', 'quote', 'user', 'messaging']
    notificationTypes.forEach(type => {
      actionRegistry.register(`${type}.mail`, MailAction)
      actionRegistry.register(`${type}.storeMail`, StoreMailAction)
      actionRegistry.register(`${type}.orderMail`, OrderMailAction)
      actionRegistry.register(`${type}.storeOrderMail`, StoreOrderMailAction)
    })

    // ============================================
    // Widget Registration
    // ============================================
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop_notification_rules',
      component: NotificationRuleManager
    })
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(NotificationBundleIconModule)
  }
}

export default plugin
