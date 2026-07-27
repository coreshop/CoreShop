/**
 * CoreShop Schema Adapter - FormSchemaAdapter
 *
 * Converts backend form schema JSON into FormBuilderConfig.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { Input } from 'antd'
import type { FormSchemaResponse, FormSchemaField } from './types'
import type { FormBuilderConfig, FieldDefinition, SectionDefinition } from '../form-builder/types'
import type { WidgetRegistry } from './WidgetRegistry'

/**
 * Convert a backend FormSchemaResponse to a FormBuilderConfig.
 *
 * Uses the WidgetRegistry to resolve block prefixes to React components.
 */
export const toFormBuilderConfig = <T = any>(
  schema: FormSchemaResponse,
  widgetRegistry: WidgetRegistry,
): FormBuilderConfig<T> => {
  const fields: FieldDefinition<T>[] = []
  const sections: SectionDefinition[] = []
  const tabs: Array<{ key: string; title: string; order: number }> = []

  // Convert schema sections
  for (const section of schema.sections) {
    sections.push({
      key: section.key,
      title: section.label,
      order: section.order,
      collapsible: section.collapsible,
      defaultCollapsed: section.defaultCollapsed,
    })
  }

  // Convert schema tabs
  for (const tab of schema.tabs) {
    tabs.push({
      key: tab.key,
      title: tab.label,
      order: tab.order,
    })
  }

  // Convert schema fields
  for (const field of schema.fields) {
    const converted = convertField<T>(field, widgetRegistry)
    if (converted) {
      fields.push(...converted)
    }
  }

  return {
    fields,
    sections: sections.length > 0 ? sections : undefined,
    tabs: tabs.length > 0 ? tabs : undefined,
  }
}

/**
 * Convert a single schema field to one or more FieldDefinitions.
 *
 * Translation fields are expanded into localized field definitions.
 */
const convertField = <T = any>(
  field: FormSchemaField,
  widgetRegistry: WidgetRegistry,
): FieldDefinition<T>[] | null => {
  const normalizedSection = normalizeOptionalSlot(field.section)
  const normalizedTab = normalizeOptionalSlot(field.tab)

  // Resolve through widget registry first (takes precedence over compound flattening).
  // This allows custom widgets for compound types (e.g. gateway_config).
  const resolved = widgetRegistry.resolve(field)

  if (resolved) {
    const { rules, componentProps } = buildValidationAndProps(field, resolved.props)

    return [{
      name: field.name,
      label: field.label ?? field.name,
      component: resolved.component,
      required: field.required,
      rules: rules.length > 0 ? rules : undefined,
      componentProps,
      valuePropName: resolved.valuePropName,
      section: normalizedSection,
      tab: normalizedTab,
      ...(resolved.extra ?? {}),
    }]
  }

  // Handle translations compound field (detected by block prefix)
  if (field.blockPrefixes.includes('coreshop_translations') && field.children) {
    return convertTranslationFields<T>(field, widgetRegistry)
  }

  // Handle generic compound fields by flattening children into nested names
  if (field.children && field.children.fields.length > 0) {
    return convertCompoundFields<T>(field, widgetRegistry)
  }

  // Fallback: text input
  const { rules: fallbackRules, componentProps: fallbackProps } = buildValidationAndProps(field)

  return [{
    name: field.name,
    label: field.label ?? field.name,
    component: Input,
    required: field.required,
    rules: fallbackRules.length > 0 ? fallbackRules : undefined,
    componentProps: fallbackProps,
    section: normalizedSection,
    tab: normalizedTab,
  }]
}

/**
 * Expand translations into localized FieldDefinitions.
 *
 * For each child field, creates a field with `localized: true`.
 */
const convertTranslationFields = <T = any>(
  translationField: FormSchemaField,
  widgetRegistry: WidgetRegistry,
): FieldDefinition<T>[] => {
  const fields: FieldDefinition<T>[] = []

  if (!translationField.children) {
    return fields
  }

  // ResourceTranslationsType structure:
  // translations -> [en, de, fr, ...] -> [label, description, ...]
  // We want localized fields to follow the global locale selector,
  // so we derive translatable field definitions from one locale entry.
  const localeEntry = translationField.children.fields.find(
    (entryField) => entryField.children && entryField.children.fields.length > 0,
  )

  if (localeEntry?.children) {
    for (const translatedField of localeEntry.children.fields) {
      const resolved = widgetRegistry.resolve(translatedField)
      const { rules, componentProps } = buildValidationAndProps(translatedField, resolved?.props)

      fields.push({
        name: translatedField.name,
        label: translatedField.label ?? translatedField.name,
        component: resolved?.component ?? Input,
        required: translatedField.required,
        localized: true,
        rules: rules.length > 0 ? rules : undefined,
        componentProps,
        valuePropName: resolved?.valuePropName,
        section: normalizeOptionalSlot(translationField.section),
        tab: normalizeOptionalSlot(translationField.tab),
        ...(resolved?.extra ?? {}),
      })
    }

    return fields
  }

  // Fallback for unexpected translation structures.
  for (const childField of translationField.children.fields) {
    if (childField.children && childField.children.fields.length > 0) {
      continue
    }

    const resolved = widgetRegistry.resolve(childField)
    const { rules, componentProps } = buildValidationAndProps(childField, resolved?.props)

    fields.push({
      name: childField.name,
      label: childField.label ?? childField.name,
      component: resolved?.component ?? Input,
      required: childField.required,
      localized: true,
      rules: rules.length > 0 ? rules : undefined,
      componentProps,
      valuePropName: resolved?.valuePropName,
      section: normalizeOptionalSlot(translationField.section),
      tab: normalizeOptionalSlot(translationField.tab),
      ...(resolved?.extra ?? {}),
    })
  }

  return fields
}

const convertCompoundFields = <T = any>(
  parentField: FormSchemaField,
  widgetRegistry: WidgetRegistry,
): FieldDefinition<T>[] => {
  const fields: FieldDefinition<T>[] = []

  if (!parentField.children) {
    return fields
  }

  for (const childField of parentField.children.fields) {
    if (childField.children && childField.children.fields.length > 0) {
      const nestedFields = convertCompoundFields<T>(childField, widgetRegistry)
      for (const nestedField of nestedFields) {
        const nestedName = Array.isArray(nestedField.name) ? nestedField.name : [nestedField.name]
        fields.push({
          ...nestedField,
          name: [parentField.name, ...nestedName],
          section: normalizeOptionalSlot(parentField.section) ?? normalizeOptionalSlot(nestedField.section),
          tab: normalizeOptionalSlot(parentField.tab) ?? normalizeOptionalSlot(nestedField.tab),
        })
      }
      continue
    }

    const resolved = widgetRegistry.resolve(childField)
    const { rules, componentProps } = buildValidationAndProps(childField, resolved?.props)

    fields.push({
      name: [parentField.name, childField.name],
      label: childField.label ?? childField.name,
      component: resolved?.component ?? Input,
      required: childField.required,
      rules: rules.length > 0 ? rules : undefined,
      componentProps,
      valuePropName: resolved?.valuePropName,
      section: normalizeOptionalSlot(parentField.section) ?? normalizeOptionalSlot(childField.section),
      tab: normalizeOptionalSlot(parentField.tab) ?? normalizeOptionalSlot(childField.tab),
      ...(resolved?.extra ?? {}),
    })
  }

  return fields
}

const buildValidationAndProps = (
  field: FormSchemaField,
  baseComponentProps?: Record<string, any>,
): { rules: Array<Record<string, any>>; componentProps: Record<string, any> | undefined } => {
  const extra = field.extra ?? {}
  const attr = (extra.attr && typeof extra.attr === 'object') ? extra.attr as Record<string, any> : {}

  const rules: Array<Record<string, any>> = []
  if (field.required) {
    rules.push({ required: true })
  }

  if (typeof attr.minlength === 'number') {
    rules.push({ min: attr.minlength })
  }
  if (typeof attr.maxlength === 'number') {
    rules.push({ max: attr.maxlength })
  }
  const numericMin = parseNumericAttribute(attr.min)
  if (numericMin !== null) {
    rules.push({ type: 'number', min: numericMin })
  }
  const numericMax = parseNumericAttribute(attr.max)
  if (numericMax !== null) {
    rules.push({ type: 'number', max: numericMax })
  }
  if (typeof attr.pattern === 'string' && attr.pattern !== '') {
    try {
      rules.push({ pattern: new RegExp(attr.pattern) })
    } catch {
      // Ignore invalid backend regex patterns and rely on server validation.
    }
  }

  const componentProps = {
    ...(baseComponentProps ?? {}),
    ...(typeof attr.placeholder === 'string' ? { placeholder: attr.placeholder } : {}),
  }

  return {
    rules,
    componentProps: Object.keys(componentProps).length > 0 ? componentProps : undefined,
  }
}

const parseNumericAttribute = (value: unknown): number | null => {
  if (typeof value === 'number' && Number.isFinite(value)) {
    return value
  }

  if (typeof value === 'string' && value.trim() !== '') {
    const parsed = Number(value)
    if (Number.isFinite(parsed)) {
      return parsed
    }
  }

  return null
}

const normalizeOptionalSlot = (value: string | null | undefined): string | undefined => {
  return value ?? undefined
}
