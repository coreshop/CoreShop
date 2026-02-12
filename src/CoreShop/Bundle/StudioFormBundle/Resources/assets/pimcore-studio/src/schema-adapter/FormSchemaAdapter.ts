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
 * Uses the WidgetRegistry to resolve widget types to React components.
 */
export const toFormBuilderConfig = <T = any>(
  schema: FormSchemaResponse,
  widgetRegistry: WidgetRegistry,
): FormBuilderConfig<T> => {
  const fields: FieldDefinition<T>[] = []
  const sections: SectionDefinition[] = []

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
  // Handle translations compound field
  if (field.uiType.widget === 'coreshop_translations' && field.children) {
    return convertTranslationFields<T>(field, widgetRegistry)
  }

  // Handle entity select by checking entityType
  if (field.uiType.widget === 'entitySelect' && field.uiType.entityType) {
    const entityResolver = widgetRegistry.resolve({
      ...field,
      uiType: { ...field.uiType, widget: field.uiType.entityType },
    })

    if (entityResolver) {
      return [{
        name: field.name,
        label: field.name,
        component: entityResolver.component,
        required: field.required,
        componentProps: entityResolver.props,
        valuePropName: entityResolver.valuePropName,
        section: field.section,
        ...(entityResolver.extra ?? {}),
      }]
    }
  }

  // Resolve through widget registry
  const resolved = widgetRegistry.resolve(field)

  if (resolved) {
    return [{
      name: field.name,
      label: field.name,
      component: resolved.component,
      required: field.required,
      componentProps: resolved.props,
      valuePropName: resolved.valuePropName,
      section: field.section,
      ...(resolved.extra ?? {}),
    }]
  }

  // Fallback: text input
  return [{
    name: field.name,
    label: field.name,
    component: Input,
    required: field.required,
    section: field.section,
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

  for (const childField of translationField.children.fields) {
    const resolved = widgetRegistry.resolve(childField)

    fields.push({
      name: childField.name,
      label: childField.name,
      component: resolved?.component ?? Input,
      required: childField.required,
      localized: true,
      componentProps: resolved?.props,
      valuePropName: resolved?.valuePropName,
      section: translationField.section,
      ...(resolved?.extra ?? {}),
    })
  }

  return fields
}
