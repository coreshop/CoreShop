/**
 * CoreShop Schema Adapter - SchemaForm Component
 *
 * Convenience component that combines useFormSchema + DynamicForm.
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
import { Spin, Alert } from 'antd'
import type { FormInstance } from 'antd/es/form'
import { DynamicForm } from '../form-builder/components/DynamicForm'
import type { FormDecorator } from '../form-builder/types'
import { useFormSchema } from './useFormSchema'

export interface SchemaFormProps<T = any> {
  /** Schema alias (e.g., 'coreshop.country') */
  alias: string
  /** Form data */
  data?: T
  /** Change handler */
  onChange: (draft: Partial<T>) => void
  /** Optional decorators */
  decorators?: Array<{ name: string; decorator: FormDecorator<T> }>
  /** Current locale for localized fields */
  currentLocale?: string
  /** Available locales */
  locales?: string[]
  /** External form instance */
  form?: FormInstance
}

/**
 * SchemaForm
 *
 * One-liner for schema-based forms with built-in loading and error states.
 *
 * @example
 * ```typescript
 * <SchemaForm
 *   alias="coreshop.country"
 *   data={countryData}
 *   onChange={handleChange}
 *   currentLocale={locale}
 * />
 * ```
 */
export const SchemaForm = <T extends Record<string, any> = any>({
  alias,
  data,
  onChange,
  decorators,
  currentLocale,
  form,
}: SchemaFormProps<T>): React.JSX.Element => {
  const { builder, loading, error } = useFormSchema<T>(alias, decorators)

  if (loading) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', padding: 24 }}>
        <Spin />
      </div>
    )
  }

  if (error) {
    return (
      <Alert
        type="error"
        message="Failed to load form"
        description={error.message}
        showIcon
      />
    )
  }

  if (!builder) {
    return (
      <Alert
        type="warning"
        message="No form configuration available"
        showIcon
      />
    )
  }

  const config = builder.build({ data, locale: currentLocale })

  return (
    <DynamicForm
      config={config}
      data={data}
      onChange={onChange}
      currentLocale={currentLocale}
      form={form}
    />
  )
}
