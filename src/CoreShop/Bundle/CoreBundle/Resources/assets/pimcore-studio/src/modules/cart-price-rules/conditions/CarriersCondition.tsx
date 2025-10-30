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

import React from 'react'
import { Form, Select } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import { useEntitySelect } from '@coreshop/resource'
import { carrierApi } from '@coreshop/shipping/src/modules/carriers/api'

export const CarriersCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const carriers = data.carriers || []
  const [options, value, handleSelectChange, loading] = useEntitySelect(carrierApi, carriers, 'identifier')

  const handleChange = (selectedIds: number[]) => {
    handleSelectChange(selectedIds)
    onChange({ ...data, carriers: selectedIds })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Carriers">
        <Select
          mode="multiple"
          value={value}
          onChange={handleChange}
          placeholder="Select carriers"
          style={{ width: '100%' }}
          loading={loading}
          options={options}
        />
      </Form.Item>
    </Form>
  )
}
