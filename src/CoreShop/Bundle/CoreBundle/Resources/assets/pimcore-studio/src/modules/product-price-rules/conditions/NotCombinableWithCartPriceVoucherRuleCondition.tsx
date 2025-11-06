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
import { cartPriceRuleApi } from '@coreshop/order/src/modules/cart-price-rules/api'

export const NotCombinableWithCartPriceVoucherRuleCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const priceRules = data.price_rules || []
  // Type cast needed because CartPriceRule has optional id, but useEntitySelect expects required id
  const [options, value, handleSelectChange, loading] = useEntitySelect(cartPriceRuleApi as any, priceRules)

  const handleChange = (selectedIds: number[]) => {
    handleSelectChange(selectedIds)
    onChange({ ...data, price_rules: selectedIds })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Not combinable with Cart Price Rules">
        <Select
          mode="multiple"
          value={value}
          onChange={handleChange}
          placeholder="Select cart price rules"
          style={{ width: '100%' }}
          loading={loading}
          showSearch
          optionFilterProp="label"
          options={options}
        />
      </Form.Item>
    </Form>
  )
}
