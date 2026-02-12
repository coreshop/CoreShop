/**
 * CoreShop Schema Adapter - useFormSchema Hook
 *
 * React hook to fetch and convert schema to FormBuilder.
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
import { container } from '@pimcore/studio-ui-bundle'
import { FormBuilder } from '../form-builder/FormBuilder'
import type { FormDecorator } from '../form-builder/types'
import { fetchFormSchema } from './api'
import { toFormBuilderConfig } from './FormSchemaAdapter'
import { WidgetRegistry } from './WidgetRegistry'
import { widgetRegistryServiceId } from '../services'

export interface UseFormSchemaResult<T> {
  /** The FormBuilder instance (null while loading) */
  builder: FormBuilder<T> | null
  /** Whether the schema is currently loading */
  loading: boolean
  /** Error if schema fetch failed */
  error: Error | null
}

/**
 * React hook that fetches a form schema and creates a FormBuilder.
 *
 * @param blockPrefix - Schema block prefix (e.g., 'coreshop_country')
 * @param decorators - Optional decorators to apply after schema conversion
 *
 * @example
 * ```typescript
 * const { builder, loading } = useFormSchema<CountryDetail>('coreshop_country', [
 *   { name: 'iso-hint', decorator: transformFieldDecorator('isoCode', (f) => ({
 *     ...f, tooltip: 'ISO 3166-1 alpha-2'
 *   }))}
 * ])
 *
 * if (loading || !builder) return <Spin />
 *
 * const config = builder.build({ data })
 * return <DynamicForm config={config} data={data} onChange={onChange} />
 * ```
 */
export const useFormSchema = <T = any>(
  blockPrefix: string,
  decorators?: Array<{ name: string; decorator: FormDecorator<T> }>,
): UseFormSchemaResult<T> => {
  const [builder, setBuilder] = React.useState<FormBuilder<T> | null>(null)
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<Error | null>(null)

  React.useEffect(() => {
    let cancelled = false

    const load = async () => {
      try {
        setLoading(true)
        setError(null)

        const schema = await fetchFormSchema(blockPrefix)

        if (cancelled) return

        const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)
        const config = toFormBuilderConfig<T>(schema, widgetRegistry)
        const newBuilder = new FormBuilder<T>(config)

        // Apply optional decorators
        if (decorators) {
          for (const { name, decorator } of decorators) {
            newBuilder.addDecorator(name, decorator)
          }
        }

        setBuilder(newBuilder)
      } catch (err) {
        if (!cancelled) {
          setError(err instanceof Error ? err : new Error(String(err)))
        }
      } finally {
        if (!cancelled) {
          setLoading(false)
        }
      }
    }

    void load()

    return () => {
      cancelled = true
    }
  }, [blockPrefix])

  return { builder, loading, error }
}
