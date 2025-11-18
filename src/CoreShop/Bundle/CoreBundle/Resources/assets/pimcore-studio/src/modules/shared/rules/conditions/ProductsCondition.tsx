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
import { useTranslation } from 'react-i18next'
import { ManyToManyRelation } from '@pimcore/studio-ui-bundle/modules/element'
import type { ManyToManyRelationValue } from '../../../../../../../../../ResourceBundle/Resources/assets/pimcore-studio/src/entities/types/relation'
import { container } from '@pimcore/studio-ui-bundle'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

interface ProductsConditionData {
  products?: string[] | ManyToManyRelationValue
  includeVariants?: boolean
}

export const ProductsCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const conditionData = data as ProductsConditionData
  const includeVariants = conditionData.includeVariants || false

  const [allowedClasses, setAllowedClasses] = useState<string[]>([])
  const [relationValue, handleRelationChange] = useRelationIds(conditionData.products, 'Product')

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  useEffect(() => {
    const loadAllowedClasses = async () => {
      const classes = await configProvider.getAllowedClasses('coreshop.product')
      setAllowedClasses(classes)
    }
    loadAllowedClasses()
  }, [configProvider])

  const handleProductsChange = (value: ManyToManyRelationValue | null) => {
    const ids = handleRelationChange(value)
    onChange({
      ...conditionData,
      products: ids
    })
  }

  const handleIncludeVariantsChange = (checked: boolean) => {
    onChange({
      ...conditionData,
      includeVariants: checked
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_report_products', { defaultValue: 'Products' })}>
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
          onChange={handleProductsChange}
        />
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={includeVariants}
          onChange={(e) => handleIncludeVariantsChange(e.target.checked)}
        >
          {t('coreshop_condition_include_variants', { defaultValue: 'Include Variants' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}
