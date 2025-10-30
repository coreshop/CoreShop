/**
 * CoreShop OrderBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useEffect } from 'react'
import { Form, Select, Spin } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import { cartPriceRuleApi } from '../api'
import type { CartPriceRule } from '../types'

interface NotCombinableConditionData {
  price_rules?: number[]
}

export const NotCombinableCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const conditionData = data as NotCombinableConditionData
  const priceRules = conditionData.price_rules || []

  const [loading, setLoading] = useState(false)
  const [rules, setRules] = useState<CartPriceRule[]>([])

  useEffect(() => {
    const loadRules = async () => {
      setLoading(true)
      try {
        const response = await cartPriceRuleApi.list()
        // list() returns an array directly, not an object with data property
        setRules(Array.isArray(response) ? response : [])
      } catch (error) {
        console.error('Failed to load cart price rules:', error)
        setRules([])
      } finally {
        setLoading(false)
      }
    }

    loadRules()
  }, [])

  const handleChange = (value: number[]) => {
    onChange({
      ...conditionData,
      price_rules: value
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Not combinable with">
        <Spin spinning={loading}>
          <Select
            mode="multiple"
            value={priceRules}
            onChange={handleChange}
            placeholder="Select cart price rules"
            style={{ width: '100%' }}
            showSearch
            optionFilterProp="label"
            options={(rules || []).map(rule => ({
              value: rule.id,
              label: rule.name
            }))}
          />
        </Spin>
      </Form.Item>
    </Form>
  )
}
