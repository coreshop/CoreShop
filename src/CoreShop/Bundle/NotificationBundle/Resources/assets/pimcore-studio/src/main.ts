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
import { Input } from 'antd'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { coreshopNotificationServiceIds } from './modules/notification-rules/service-ids'
import { NotificationRuleManager } from './modules/notification-rules/NotificationRuleManager'
import { LocalizedMailDocumentsWidget } from './modules/notification-rules/widgets/LocalizedMailDocumentsWidget'
import { StoreLocalizedMailDocumentsWidget } from './modules/notification-rules/widgets/StoreLocalizedMailDocumentsWidget'

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
    const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

    // Hide NotificationBundle-owned rule collection prefixes from generic schema forms
    const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
    ;[
      'coreshop_notification_rule_condition_collection',
      'coreshop_notification_action_collection',
    ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))

    // Symfony/Twig-like block-prefix override:
    // Notification type is rendered by dedicated custom selector in SettingsForm.
    formWidgetRegistry.register('coreshop_notification_rule_type', () => ({
      component: Input,
      extra: {
        hidden: true,
      },
    }))

    // Register custom widgets for localized mail document fields
    formWidgetRegistry.register('coreshop_localized_mail_documents', () => ({
      component: LocalizedMailDocumentsWidget,
    }))

    formWidgetRegistry.register('coreshop_store_localized_mail_documents', () => ({
      component: StoreLocalizedMailDocumentsWidget,
    }))

    moduleSystem.registerModule(NotificationBundleIconModule)
  }
}

export default plugin
