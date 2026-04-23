/**
 * Registers schema-based rule components from backend type->blockPrefix mappings.
 */

import type { RuleConfig } from '../types'
import { createSchemaAction, createSchemaCondition } from '../components'
import { ActionRegistry } from './ActionRegistry'
import { ConditionRegistry } from './ConditionRegistry'

export interface SchemaRegistrationOptions {
  overwriteExisting?: boolean
}

const isSchemaConditionComponent = (component: unknown): boolean => {
  const displayName = (component as { displayName?: string })?.displayName
  return typeof displayName === 'string' && displayName.startsWith('SchemaCondition(')
}

const isSchemaActionComponent = (component: unknown): boolean => {
  const displayName = (component as { displayName?: string })?.displayName
  return typeof displayName === 'string' && displayName.startsWith('SchemaAction(')
}

const getSchemaFieldCount = (
  schemas: Record<string, any> | undefined,
  blockPrefix: string | undefined,
): number => {
  if (!schemas || !blockPrefix) {
    return 0
  }

  const fields = schemas[blockPrefix]?.fields
  return Array.isArray(fields) ? fields.length : 0
}

const findFallbackSchemaPrefix = (
  kind: 'condition' | 'action',
  type: string,
  mappedPrefix: string | undefined,
  schemas: Record<string, any> | undefined,
): string | undefined => {
  if (!schemas) {
    return undefined
  }

  const candidates = Object.entries(schemas)
    .filter(([prefix, schema]) => {
      if (!prefix.includes(`_${kind}_`) || !prefix.endsWith(`_${type}`)) {
        return false
      }

      const fields = (schema as Record<string, any>)?.fields
      return Array.isArray(fields) && fields.length > 0
    })
    .map(([prefix]) => prefix)

  if (candidates.length === 0) {
    return undefined
  }

  if (mappedPrefix) {
    const kindMarker = `_${kind}_`
    const markerIndex = mappedPrefix.indexOf(kindMarker)
    if (markerIndex !== -1) {
      const namespacePrefix = mappedPrefix.slice(0, markerIndex + kindMarker.length)
      const scopedCandidate = candidates.find((prefix) => prefix.startsWith(namespacePrefix))
      if (scopedCandidate) {
        return scopedCandidate
      }
    }
  }

  return candidates[0]
}

const resolveSchemaPrefix = (
  kind: 'condition' | 'action',
  type: string,
  mappedPrefix: string,
  schemas: Record<string, any> | undefined,
): string => {
  // Fast path: mapping exists and resolves to a non-empty schema.
  if (getSchemaFieldCount(schemas, mappedPrefix) > 0) {
    return mappedPrefix
  }

  // Fallback for inconsistent mappings: try to resolve by kind + type suffix.
  const fallbackPrefix = findFallbackSchemaPrefix(kind, type, mappedPrefix, schemas)
  if (fallbackPrefix) {
    return fallbackPrefix
  }

  // Keep original mapping for intentionally empty schemas.
  return mappedPrefix
}

export const registerSchemaComponentsFromMaps = (
  conditionRegistry: ConditionRegistry,
  actionRegistry: ActionRegistry,
  conditionSchemaByType: Record<string, string> | undefined,
  actionSchemaByType: Record<string, string> | undefined,
  schemas: Record<string, any> | undefined = undefined,
  options: SchemaRegistrationOptions = {},
): void => {
  const overwriteExisting = options.overwriteExisting ?? false

  for (const [type, mappedBlockPrefix] of Object.entries(conditionSchemaByType ?? {})) {
    const blockPrefix = resolveSchemaPrefix('condition', type, mappedBlockPrefix, schemas)
    const existing = conditionRegistry.get(type)
    const shouldRegister = overwriteExisting || !existing || isSchemaConditionComponent(existing)
    if (shouldRegister) {
      conditionRegistry.register(type, createSchemaCondition(blockPrefix))
    }
  }

  for (const [type, mappedBlockPrefix] of Object.entries(actionSchemaByType ?? {})) {
    const blockPrefix = resolveSchemaPrefix('action', type, mappedBlockPrefix, schemas)
    const existing = actionRegistry.get(type)
    const shouldRegister = overwriteExisting || !existing || isSchemaActionComponent(existing)
    if (shouldRegister) {
      actionRegistry.register(type, createSchemaAction(blockPrefix))
    }
  }
}

export const registerSchemaComponentsFromConfig = (
  conditionRegistry: ConditionRegistry,
  actionRegistry: ActionRegistry,
  config: RuleConfig,
  options: SchemaRegistrationOptions = {},
): void => {
  registerSchemaComponentsFromMaps(
    conditionRegistry,
    actionRegistry,
    config.conditionSchemaByType,
    config.actionSchemaByType,
    config.schemas,
    options,
  )
}
