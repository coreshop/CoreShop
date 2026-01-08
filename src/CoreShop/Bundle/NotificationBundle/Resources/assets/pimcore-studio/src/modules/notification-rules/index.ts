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

export { NotificationRuleManager } from './NotificationRuleManager'
export { notificationRuleApi, NotificationRuleApi } from './api'
export { coreshopNotificationServiceIds } from './service-ids'
export type { NotificationRule, NotificationRuleType, NotificationRuleConfig } from './types'

// Conditions
export { AbstractStateCondition, type AbstractStateConditionProps } from './conditions'

// Actions
export { MailAction } from './actions'

// Components
export { SettingsForm } from './components'
