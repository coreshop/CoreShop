/**
 * CoreShop RuleBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface RuleCondition {
  id?: number
  type: string
  configuration: Record<string, any>
  sort?: number
}

export interface RuleAction {
  id?: number
  type: string
  configuration: Record<string, any>
  sort?: number
}

export interface Rule {
  id?: number
  name: string
  description?: string
  active?: boolean
  priority?: number
  conditions?: RuleCondition[]
  actions?: RuleAction[]
}

export interface RuleConfig {
  conditions: string[]
  actions: string[]
  conditionSchemaByType?: Record<string, string>
  actionSchemaByType?: Record<string, string>
  schemas?: Record<string, any>
  [key: string]: any
}

export interface ConditionComponentProps {
  data: Record<string, any>
  onChange: (data: Record<string, any>) => void
  type?: string
  currentLocale?: string
  locales?: string[]
  registryId?: symbol | string  // Optional: required for nested conditions
}

export interface ActionComponentProps {
  data: Record<string, any>
  onChange: (data: Record<string, any>) => void
  type?: string
  currentLocale?: string
  locales?: string[]
  registryId?: symbol | string  // Optional: required for nested actions
}
