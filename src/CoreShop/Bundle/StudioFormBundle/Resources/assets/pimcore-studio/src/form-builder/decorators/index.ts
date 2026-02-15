/**
 * CoreShop Form Builder - Standard Decorators
 *
 * Collection of commonly used decorators.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { FormDecorator, FieldDefinition, SectionDefinition } from '../types'

/**
 * Sort sections by order
 */
export const sectionSortingDecorator: FormDecorator = (config) => {
  if (!config.sections) return config

  const sorted = [...config.sections].sort((a, b) => {
    const orderA = a.order ?? 999
    const orderB = b.order ?? 999
    return orderA - orderB
  })

  return {
    ...config,
    sections: sorted
  }
}

/**
 * Filter hidden fields
 */
export const hiddenFieldsDecorator: FormDecorator = (config, context) => {
  return {
    ...config,
    fields: config.fields.filter(field => {
      if (field.hidden === true) return false
      if (field.visible && !field.visible(context?.data)) return false
      return true
    })
  }
}

/**
 * Add field to specific section
 */
export const addFieldDecorator = <T = any>(
  field: FieldDefinition<T>,
  position: 'start' | 'end' | number = 'end'
): FormDecorator<T> => {
  return (config) => {
    const fields = [...config.fields]

    if (position === 'start') {
      fields.unshift(field)
    } else if (position === 'end') {
      fields.push(field)
    } else {
      fields.splice(position, 0, field)
    }

    return {
      ...config,
      fields
    }
  }
}

/**
 * Remove field by name
 */
export const removeFieldDecorator = <T = any>(
  fieldName: string
): FormDecorator<T> => {
  return (config) => ({
    ...config,
    fields: config.fields.filter(f => f.name !== fieldName)
  })
}

/**
 * Add section
 */
export const addSectionDecorator = <T = any>(
  section: SectionDefinition
): FormDecorator<T> => {
  return (config) => ({
    ...config,
    sections: [
      ...(config.sections ?? []),
      section
    ]
  })
}

/**
 * Make all fields readonly
 */
export const readonlyDecorator: FormDecorator = (config) => {
  return {
    ...config,
    fields: config.fields.map(field => ({
      ...field,
      disabled: true
    }))
  }
}

/**
 * Transform field
 */
export const transformFieldDecorator = <T = any>(
  fieldName: string,
  transform: (field: FieldDefinition<T>) => FieldDefinition<T>
): FormDecorator<T> => {
  return (config) => ({
    ...config,
    fields: config.fields.map(field =>
      field.name === fieldName ? transform(field) : field
    )
  })
}

/**
 * Add validation rules to field
 */
export const addValidationDecorator = <T = any>(
  fieldName: string,
  rules: any[]
): FormDecorator<T> => {
  return transformFieldDecorator(fieldName, (field) => ({
    ...field,
    rules: [...(field.rules ?? []), ...rules]
  }))
}

/**
 * Set field as required
 */
export const requiredFieldDecorator = <T = any>(
  fieldName: string,
  message?: string
): FormDecorator<T> => {
  return transformFieldDecorator(fieldName, (field) => ({
    ...field,
    required: true,
    rules: [
      ...(field.rules ?? []),
      { required: true, message: message ?? `${field.label} is required` }
    ]
  }))
}

/**
 * Conditional fields decorator
 *
 * Shows/hides fields based on data condition
 */
export const conditionalFieldsDecorator = <T = any>(
  condition: (data?: T) => boolean,
  fieldNames: string[]
): FormDecorator<T> => {
  return (config, context) => {
    const shouldShow = condition(context?.data)

    return {
      ...config,
      fields: config.fields.map(field =>
        fieldNames.includes(field.name as string)
          ? { ...field, hidden: !shouldShow }
          : field
      )
    }
  }
}

/**
 * Filter to only show fields and sections matching a specific section key.
 *
 * Useful for multi-step wizards where each step renders a subset of the schema.
 */
export const sectionFilterDecorator = <T = any>(
  sectionKey: string
): FormDecorator<T> => {
  return (config) => ({
    ...config,
    fields: config.fields.filter(f => f.section === sectionKey),
    sections: config.sections?.filter(s => s.key === sectionKey) ?? [],
  })
}

/**
 * Group fields into sections decorator
 */
export const groupFieldsDecorator = <T = any>(
  groups: Record<string, string[]>
): FormDecorator<T> => {
  return (config) => {
    const sections: SectionDefinition[] = Object.keys(groups).map(sectionKey => ({
      key: sectionKey,
      title: sectionKey,
      collapsible: true
    }))

    const fields = config.fields.map(field => {
      for (const [sectionKey, fieldNames] of Object.entries(groups)) {
        if (fieldNames.includes(field.name as string)) {
          return { ...field, section: sectionKey }
        }
      }
      return field
    })

    return {
      ...config,
      fields,
      sections: [...(config.sections ?? []), ...sections]
    }
  }
}
