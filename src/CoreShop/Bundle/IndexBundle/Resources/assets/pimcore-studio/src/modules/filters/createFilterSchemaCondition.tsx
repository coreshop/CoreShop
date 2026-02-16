/**
 * CoreShop IndexBundle - Filter Schema Condition Factory
 *
 * Creates schema-based filter condition components that:
 * - Wrap content in FilterIndexContext.Provider (provides indexId to widgets)
 * - Handle entity-level fields (label, quantityUnit) outside configuration
 * - Map between FilterCondition shape and SchemaForm flat data
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useCallback, useMemo } from 'react'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter/SchemaForm'
import type { FormDecorator } from '@coreshop/studio-form/src/form-builder'
import type { ConditionProps, FilterCondition } from './types'
import { FilterIndexContext } from './FilterIndexContext'
import { Input } from 'antd'
import { QuantityUnitSelect } from '../shared/QuantityUnitSelect'

const ENTITY_FIELDS = new Set(['label', 'quantityUnit'])

export const createFilterSchemaCondition = (blockPrefix: string): React.FC<ConditionProps> => {
  const FilterSchemaCondition: React.FC<ConditionProps> = ({ data, onChange, indexId }) => {
    const entityDecorators = useMemo<Array<{ name: string; decorator: FormDecorator<any> }>>(
      () => [
        {
          name: 'entity-fields',
          decorator: (config) => ({
            ...config,
            fields: [
              {
                name: 'label',
                label: 'coreshop_label',
                component: Input,
              },
              {
                name: 'quantityUnit',
                label: 'coreshop_filters_quantityUnit',
                component: QuantityUnitSelect,
              },
              ...config.fields,
            ],
          }),
        },
      ],
      [],
    )

    // Merge entity-level and configuration-level data into flat object for SchemaForm
    const schemaData = useMemo(
      () => ({
        label: data.label ?? '',
        quantityUnit: data.quantityUnit ?? '0',
        ...(data.configuration ?? {}),
      }),
      [data],
    )

    const handleChange = useCallback(
      (draft: Record<string, any>) => {
        const entityUpdate: Partial<FilterCondition> = {}
        const configUpdate: Record<string, any> = {}

        for (const [key, value] of Object.entries(draft)) {
          if (ENTITY_FIELDS.has(key)) {
            ;(entityUpdate as any)[key] = key === 'quantityUnit' ? Number(value) : value
          } else {
            configUpdate[key] = value
          }
        }

        onChange({
          ...entityUpdate,
          ...(Object.keys(configUpdate).length > 0
            ? { configuration: { ...data.configuration, ...configUpdate } }
            : {}),
        })
      },
      [data.configuration, onChange],
    )

    const contextValue = useMemo(() => ({ indexId }), [indexId])

    return (
      <FilterIndexContext.Provider value={contextValue}>
        <SchemaForm
          blockPrefix={blockPrefix}
          data={schemaData}
          onChange={handleChange}
          decorators={entityDecorators}
        />
      </FilterIndexContext.Provider>
    )
  }
  FilterSchemaCondition.displayName = `FilterSchemaCondition(${blockPrefix})`
  return FilterSchemaCondition
}
