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
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const CurrenciesCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const currencies = data.currencies || []
  const [options, value, handleSelectChange, loading] = useEntitySelect(currencyApi, currencies)

  const handleChange = (selectedIds: number[]) => {
    handleSelectChange(selectedIds)
    onChange({ ...data, currencies: selectedIds })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Currencies">
        <Select
          mode="multiple"
          value={value}
          onChange={handleChange}
          placeholder="Select currencies"
          style={{ width: '100%' }}
          loading={loading}
          options={options}
        />
      </Form.Item>
    </Form>
  )
}
