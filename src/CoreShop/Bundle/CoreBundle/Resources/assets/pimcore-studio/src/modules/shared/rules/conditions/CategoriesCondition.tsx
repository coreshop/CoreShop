/**
 * CoreShop CoreBundle Studio Plugin
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
import { Form, Checkbox } from 'antd'
import { ManyToManyRelation } from '@pimcore/studio-ui-bundle/modules/element'
import type { ManyToManyRelationValue } from '../../../../../../../../../ResourceBundle/Resources/assets/pimcore-studio/src/entities/types/relation'
import { container } from '@pimcore/studio-ui-bundle'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

interface CategoriesConditionData {
  categories?: string[] | ManyToManyRelationValue
  recursive?: boolean
}

export const CategoriesCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const conditionData = data as CategoriesConditionData
  const recursive = conditionData.recursive || false

  const [allowedClasses, setAllowedClasses] = useState<string[]>([])
  const [relationValue, handleRelationChange] = useRelationIds(conditionData.categories, 'Category')

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
      ...conditionData,
      categories: ids
    })
  }

  const handleRecursiveChange = (checked: boolean) => {
    onChange({
      ...conditionData,
      recursive: checked
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Categories">
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

      <Form.Item>
        <Checkbox
          checked={recursive}
          onChange={(e) => handleRecursiveChange(e.target.checked)}
        >
          Include Subcategories
        </Checkbox>
      </Form.Item>
    </Form>
  )
}
