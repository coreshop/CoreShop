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

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const GiftProductAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const product = data.product || null
  const quantity = data.quantity || 1

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_action_giftProduct', { defaultValue: 'Gift Product' })}>
        <InputNumber
          value={product}
          onChange={(value) => handleChange('product', value)}
          min={0}
          precision={0}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_condition_quantity', { defaultValue: 'Quantity' })}>
        <InputNumber
          value={quantity}
          onChange={(value) => handleChange('quantity', value || 1)}
          min={1}
          precision={0}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
