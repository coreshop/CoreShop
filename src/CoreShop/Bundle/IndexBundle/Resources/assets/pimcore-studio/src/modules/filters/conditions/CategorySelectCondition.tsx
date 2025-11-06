/**
 * CoreShop IndexBundle Category Select Filter Condition
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
import { Form, Checkbox, Input } from 'antd'
import type { ConditionProps } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { container } from '@pimcore/studio-ui-bundle'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'

/**
 * Category Select Condition - Single category filter
 *
 * Form fields (from FilterConditionCategorySelectType):
 * - preSelect: Default selected category ID
 * - includeSubCategories: Include subcategories
 */
export const CategorySelectCondition: React.FC<ConditionProps> = ({
  data,
  onChange
}) => {
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])

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

  const handleCategoryChange = (id: number | null) => {
    onChange({
      configuration: { ...data.configuration, preSelect: id }
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

      <Form.Item label="Category" help="Select a category">
        <DroppableEntity
          allowedClasses={allowedClasses}
          dataObjectsAllowed={true}
          assetsAllowed={false}
          documentsAllowed={false}
          value={data.configuration?.preSelect}
          onChange={handleCategoryChange}
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
    </Form>
  )
}
