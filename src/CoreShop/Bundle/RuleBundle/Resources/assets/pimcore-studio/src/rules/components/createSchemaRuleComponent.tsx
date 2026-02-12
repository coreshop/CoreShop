/**
 * CoreShop RuleBundle - Schema-based Rule Component Factory
 *
 * Creates condition/action components that render forms dynamically
 * from backend form type schemas instead of hand-written React forms.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter/SchemaForm'
import type { ConditionComponentProps, ActionComponentProps } from '../types'

/**
 * Create a schema-based condition component.
 *
 * The component renders its configuration form dynamically from the backend
 * form schema, eliminating the need for hand-written React form components.
 *
 * @param blockPrefix - Block prefix of the PHP configuration form type
 *                      (e.g., 'coreshop_cart_price_rule_condition_amount')
 *
 * @example
 * ```typescript
 * // In OrderBundle main.ts:
 * conditionRegistry.register('amount', createSchemaCondition('coreshop_cart_price_rule_condition_amount'))
 * conditionRegistry.register('voucher', createSchemaCondition('coreshop_cart_price_rule_condition_voucher'))
 * ```
 */
export const createSchemaCondition = (blockPrefix: string): React.FC<ConditionComponentProps> => {
  const SchemaCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => (
    <SchemaForm
      blockPrefix={blockPrefix}
      data={data}
      onChange={(draft) => onChange({ ...data, ...draft })}
    />
  )
  SchemaCondition.displayName = `SchemaCondition(${blockPrefix})`
  return SchemaCondition
}

/**
 * Create a schema-based action component.
 *
 * @param blockPrefix - Block prefix of the PHP configuration form type
 *                      (e.g., 'coreshop_cart_price_rule_action_surcharge_percent')
 *
 * @example
 * ```typescript
 * // In OrderBundle main.ts:
 * actionRegistry.register('surchargePercent', createSchemaAction('coreshop_cart_price_rule_action_surcharge_percent'))
 * ```
 */
export const createSchemaAction = (blockPrefix: string): React.FC<ActionComponentProps> => {
  const SchemaAction: React.FC<ActionComponentProps> = ({ data, onChange }) => (
    <SchemaForm
      blockPrefix={blockPrefix}
      data={data}
      onChange={(draft) => onChange({ ...data, ...draft })}
    />
  )
  SchemaAction.displayName = `SchemaAction(${blockPrefix})`
  return SchemaAction
}
