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
import { useTranslation } from 'react-i18next'
import type { ConditionProps } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'
import { ManyToManyRelation } from '@pimcore/studio-ui-bundle/modules/element'
import type { ManyToManyRelationValue } from '@coreshop/resource/src/entities/types/relation'
import { container } from '@pimcore/studio-ui-bundle'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

export const CategoryMultiSelectCondition: React.FC<ConditionProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
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
      <Form.Item label={t('coreshop_label', { defaultValue: 'Label' })}>
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_quantityUnit', { defaultValue: 'Quantity Value' })}>
        <QuantityUnitSelect
          value={data.quantityUnit ?? "0"}
          onChange={(value) => onChange({ quantityUnit: Number(value) })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_category_name', { defaultValue: 'Category' })}>
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
          checked={data.configuration?.includeSubCategories ?? false}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, includeSubCategories: e.target.checked }
          })}
        >
          {t('coreshop_filters_include_subcategories', { defaultValue: 'Include Subcategories' })}
        </Checkbox>
      </Form.Item>

      <Form.Item label={t('coreshop_filters_search_patterns_concatenator', { defaultValue: 'Choose concatenator' })}>
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
