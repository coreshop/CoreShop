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
import {
  ManyToManyRelation,
  ManyToOneRelation
} from '@pimcore/studio-ui-bundle/modules/element'
import { useRelationIds } from '@coreshop/resource'
import type { ManyToManyRelationValue } from '@coreshop/resource'

interface PimcoreRelationWidgetProps {
  value?: number | number[]
  onChange?: (value: number | number[] | undefined) => void
  autocompleteClass?: string
  multiple?: boolean
}

export const PimcoreRelationWidget: React.FC<PimcoreRelationWidgetProps> = ({
  value,
  onChange,
  autocompleteClass,
  multiple = false,
}) => {
  const stringIds = React.useMemo(() => {
    if (value == null) return undefined
    const ids = Array.isArray(value) ? value : [value]
    if (ids.length === 0) return undefined
    return ids.map(String)
  }, [value])

  const [relationValue, handleChange] = useRelationIds(
    stringIds,
    autocompleteClass ?? 'Entity',
    'object'
  )

  const handleMultiChange = React.useCallback((newValue: ManyToManyRelationValue | null) => {
    const ids = handleChange(newValue)
    onChange?.(ids.map(Number))
  }, [handleChange, onChange])

  if (multiple) {
    return (
      <ManyToManyRelation
        value={relationValue}
        onChange={handleMultiChange}
        allowedClasses={autocompleteClass ? [autocompleteClass] : []}
        dataObjectsAllowed
        allowedDataObjectTypes={['object', 'variant']}
        assetsAllowed={false}
        documentsAllowed={false}
        allowToClearRelation
        maxItems={null}
        width="100%"
        height={200}
        pathFormatterClass={null}
      />
    )
  }

  const singleValue = relationValue?.[0] ?? null

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
    <ManyToOneRelation
      value={singleValue}
      onChange={handleSingleChange}
      allowedClasses={autocompleteClass ? [autocompleteClass] : []}
      dataObjectsAllowed
      allowedDataObjectTypes={['object', 'variant']}
      assetsAllowed={false}
      documentsAllowed={false}
      allowToClearRelation
      width="100%"
      pathFormatterClass={null}
    />
  )
}
