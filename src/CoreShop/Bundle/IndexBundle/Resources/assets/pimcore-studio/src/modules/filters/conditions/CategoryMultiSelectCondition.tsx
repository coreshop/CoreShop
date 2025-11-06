/**
 * CoreShop IndexBundle Category MultiSelect Filter Condition
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useMemo, useState, useEffect } from 'react'
import { Form, Input, Checkbox, Select } from 'antd'
import type { ConditionProps } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'
import { ManyToManyRelation } from '@pimcore/studio-ui-bundle/modules/element'
import type { ManyToManyRelationValue } from '@coreshop/resource/src/entities/types/relation'
import { container } from '@pimcore/studio-ui-bundle'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

/**
 * Category MultiSelect Condition - Multiple categories filter
 *
 * Form fields (from FilterConditionCategoryMultiSelectType):
 * - preSelects: Array of default selected category IDs
 * - includeSubCategories: Include subcategories
 * - concatenator: OR/AND logic
 */
export const CategoryMultiSelectCondition: React.FC<ConditionProps> = ({
  data,
  onChange
}) => {
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])
  const [relationValue, handleRelationChange] = useRelationIds(data.configuration?.preSelects, 'Category')

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  useEffect(() => {
    const loadAllowedClasses = async () => {
      const classes = await configProvider.getAllowedClasses('coreshop.category')
      setAllowedClasses(classes)
    }
    loadAllowedClasses()
  }, [configProvider])

  const handleCategoriesChange = (value: ManyToManyRelationValue | null) => {
    const ids = handleRelationChange(value)
    onChange({
      configuration: { ...data.configuration, preSelects: ids }
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Label" help="Display label for the filter">
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
          placeholder="Filter label"
        />
      </Form.Item>

      <Form.Item label="Quantity Unit" help="Unit for quantity values">
        <QuantityUnitSelect
          value={data.quantityUnit ?? "0"}
          onChange={(value) => onChange({ quantityUnit: value })}
        />
      </Form.Item>

      <Form.Item label="Categories" help="Select multiple categories">
        <ManyToManyRelation
          allowedClasses={allowedClasses}
          dataObjectsAllowed={true}
          assetsAllowed={false}
          documentsAllowed={false}
          allowToClearRelation={false}
          maxItems={null}
          pathFormatterClass={null}
          width={null}
          height={null}
          value={relationValue}
          onChange={handleCategoriesChange}
        />
      </Form.Item>

      <Form.Item help="Include all subcategories in filter">
        <Checkbox
          checked={data.configuration?.includeSubCategories ?? false}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, includeSubCategories: e.target.checked }
          })}
        >
          Include Subcategories
        </Checkbox>
      </Form.Item>

      <Form.Item label="Concatenator" help="Logic operator for multiple categories">
        <Select
          value={data.configuration?.concatenator ?? 'OR'}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, concatenator: value }
          })}
          options={[
            { label: 'OR', value: 'OR' },
            { label: 'AND', value: 'AND' }
          ]}
        />
      </Form.Item>
    </Form>
  )
}
