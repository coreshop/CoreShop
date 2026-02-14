/**
 * CoreShop CoreBundle - Pimcore Relation Widget
 *
 * Wraps Pimcore's ManyToManyRelation / ManyToOneRelation components
 * for use in schema-based forms. Converts between form data (numeric IDs)
 * and ManyToManyRelationValue format.
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
import { Spin } from 'antd'
import {
  ManyToManyRelation,
  ManyToOneRelation
} from '@pimcore/studio-ui-bundle/modules/element'
import { container } from '@pimcore/studio-ui-bundle'
import { useRelationIds } from '@coreshop/resource'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import type { ManyToManyRelationValue } from '@coreshop/resource/src/entities/types/relation'

interface PimcoreRelationWidgetProps {
  value?: string | number | Array<string | number>
  onChange?: (value: string | number | Array<string | number> | undefined) => void
  autocompleteClass?: string
  multiple?: boolean
}

export const PimcoreRelationWidget: React.FC<PimcoreRelationWidgetProps> = ({
  value,
  onChange,
  autocompleteClass,
  multiple = false,
}) => {
  const [allowedClasses, setAllowedClasses] = React.useState<string[] | undefined>(undefined)

  React.useEffect(() => {
    let cancelled = false

    if (!autocompleteClass) {
      setAllowedClasses(undefined)
      return () => {
        cancelled = true
      }
    }

    const resourceType = inferResourceType(autocompleteClass)
    if (!resourceType) {
      setAllowedClasses(undefined)
      return () => {
        cancelled = true
      }
    }

    const loadAllowedClasses = async () => {
      try {
        const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const resolvedClasses = await configProvider.getAllowedClasses(resourceType)

        if (!cancelled) {
          setAllowedClasses(resolvedClasses.length > 0 ? resolvedClasses : undefined)
        }
      } catch {
        if (!cancelled) {
          setAllowedClasses(undefined)
        }
      }
    }

    void loadAllowedClasses()

    return () => {
      cancelled = true
    }
  }, [autocompleteClass])

  const stringIds = React.useMemo(() => {
    if (value == null) return undefined
    const ids = Array.isArray(value) ? value : [value]
    if (ids.length === 0) return undefined
    return ids.map(String)
  }, [value])

  const [relationValue, handleChange, loading] = useRelationIds(
    stringIds,
    autocompleteClass ?? 'Entity',
    'object'
  )

  const handleMultiChange = React.useCallback((newValue?: ManyToManyRelationValue | null) => {
    // Pimcore SDK may call onChange with undefined in intermediate states.
    // Ignore those to avoid clearing values right after selection.
    if (typeof newValue === 'undefined') {
      return
    }

    // Ensure SDK always receives a valid element type.
    const sanitizedValue = newValue?.map(item => ({
      ...item,
      type: (typeof item.type === 'string' && item.type.length > 0) ? item.type : 'object',
    })) ?? null

    const ids = handleChange(sanitizedValue)
    onChange?.(ids)
  }, [handleChange, onChange])

  if (multiple) {
    return (
      <Spin spinning={loading}>
        <ManyToManyRelation
          value={relationValue}
          onChange={handleMultiChange}
          allowedClasses={allowedClasses}
          dataObjectsAllowed
          allowedDataObjectTypes={['object', 'variant']}
          assetsAllowed={false}
          documentsAllowed={false}
          allowToClearRelation={false}
          maxItems={null}
          width="100%"
          height={200}
          pathFormatterClass={null}
        />
      </Spin>
    )
  }

  const singleValue = relationValue?.[0]
    ? {
      ...relationValue[0],
      subtype: relationValue[0].subtype ?? undefined,
    }
    : undefined

  const handleSingleChange = React.useCallback((newValue: any) => {
    if (!newValue) {
      handleChange(null)
      onChange?.(undefined)
      return
    }
    handleChange([newValue])
    onChange?.(newValue.id)
  }, [handleChange, onChange])

  return (
    <Spin spinning={loading}>
      <ManyToOneRelation
        value={singleValue}
        onChange={handleSingleChange}
        allowedClasses={allowedClasses}
        dataObjectsAllowed
        allowedDataObjectTypes={['object', 'variant']}
        assetsAllowed={false}
        documentsAllowed={false}
        allowToClearRelation
        width="100%"
        pathFormatterClass={undefined}
      />
    </Spin>
  )
}

const inferResourceType = (autocompleteClass: string): string | undefined => {
  if (!autocompleteClass.startsWith('CoreShop') || autocompleteClass.length <= 'CoreShop'.length) {
    return undefined
  }

  const name = autocompleteClass.slice('CoreShop'.length)
  const snakeCase = name.replace(/([a-z0-9])([A-Z])/g, '$1_$2').toLowerCase()

  return `coreshop.${snakeCase}`
}
