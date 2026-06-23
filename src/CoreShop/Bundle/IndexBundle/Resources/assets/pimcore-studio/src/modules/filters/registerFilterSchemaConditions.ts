/**
 * CoreShop IndexBundle - Register Filter Schema Conditions
 *
 * Registers schema-based filter condition components from backend type->blockPrefix mappings.
 * Similar to RuleBundle's registerSchemaComponentsFromMaps but uses createFilterSchemaCondition.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { ConditionRegistry } from './conditions/ConditionRegistry'
import { createFilterSchemaCondition } from './createFilterSchemaCondition'

const isFilterSchemaComponent = (component: unknown): boolean => {
  const displayName = (component as { displayName?: string })?.displayName
  return typeof displayName === 'string' && displayName.startsWith('FilterSchemaCondition(')
}

export const registerFilterSchemaConditionsFromMap = (
  registry: ConditionRegistry,
  schemaByType: Record<string, string> | undefined,
  schemas: Record<string, any> | undefined,
): void => {
  if (!schemaByType) return

  for (const [type, blockPrefix] of Object.entries(schemaByType)) {
    const existing = registry.get(type)
    // Only register if no existing component, or existing is also a schema component
    const shouldRegister = !existing || isFilterSchemaComponent(existing)
    if (shouldRegister) {
      registry.register(type, createFilterSchemaCondition(blockPrefix))
    }
  }
}
