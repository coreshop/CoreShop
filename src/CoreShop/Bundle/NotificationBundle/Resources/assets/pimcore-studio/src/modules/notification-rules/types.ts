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

import type { Rule, RuleCondition, RuleAction } from '@coreshop/rule/src/rules'

/**
 * Notification rule types - each type has its own set of conditions and actions
 */
export type NotificationRuleType =
  | 'order'
  | 'payment'
  | 'invoice'
  | 'shipment'
  | 'quote'
  | 'user'
  | 'messaging'

export interface NotificationRule extends Rule {
  id?: number
  name: string
  type?: NotificationRuleType
  active: boolean
  sort?: number
  conditions?: NotificationRuleCondition[]
  actions?: NotificationRuleAction[]
  translations?: Record<string, { label?: string }>
}

export interface NotificationRuleCondition extends RuleCondition {
  // The type is prefixed with the notification type, e.g., "order.orderState"
  type: string
}

export interface NotificationRuleAction extends RuleAction {
  // The type is prefixed with the notification type, e.g., "order.mail"
  type: string
}

export interface NotificationRuleConfig {
  types: NotificationRuleType[]
  conditions: Record<NotificationRuleType, string[]>
  actions: Record<NotificationRuleType, string[]>
  conditionSchemaByType?: Record<NotificationRuleType, Record<string, string>>
  actionSchemaByType?: Record<NotificationRuleType, Record<string, string>>
  schemas?: Record<string, any>
}
