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
import { storeApi } from '@coreshop/store/src/modules/stores/api'

export const StoresCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const stores = data.stores || []
  const [options, value, handleSelectChange, loading] = useEntitySelect(storeApi, stores)

  const handleChange = (selectedIds: number[]) => {
    handleSelectChange(selectedIds)
    onChange({ ...data, stores: selectedIds })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Stores">
        <Select
          mode="multiple"
          value={value}
          onChange={handleChange}
          placeholder="Select stores"
          style={{ width: '100%' }}
          loading={loading}
          options={options}
        />
      </Form.Item>
    </Form>
  )
}
