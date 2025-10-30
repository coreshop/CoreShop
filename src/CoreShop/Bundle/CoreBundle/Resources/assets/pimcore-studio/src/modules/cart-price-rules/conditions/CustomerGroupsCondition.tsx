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
import { Form } from 'antd'
import { ManyToManyRelation, type ManyToManyRelationValue } from '@pimcore/studio-ui-bundle/modules/element'
import { container } from '@pimcore/studio-ui-bundle'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useRelationIds } from '@coreshop/resource'

interface CustomerGroupsConditionData {
  customerGroups?: string[] | ManyToManyRelationValue
}

export const CustomerGroupsCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const conditionData = data as CustomerGroupsConditionData

  const [allowedClasses, setAllowedClasses] = useState<string[]>([])
  const [relationValue, handleRelationChange] = useRelationIds(conditionData.customerGroups, 'CustomerGroup')

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  useEffect(() => {
    const loadAllowedClasses = async () => {
      const classes = await configProvider.getAllowedClasses('coreshop.customer_group')
      setAllowedClasses(classes)
    }
    loadAllowedClasses()
  }, [configProvider])

  const handleCustomerGroupsChange = (value: ManyToManyRelationValue | null) => {
    const ids = handleRelationChange(value)
    onChange({
      ...conditionData,
      customerGroups: ids
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Customer Groups">
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
          onChange={handleCustomerGroupsChange}
        />
      </Form.Item>
    </Form>
  )
}
