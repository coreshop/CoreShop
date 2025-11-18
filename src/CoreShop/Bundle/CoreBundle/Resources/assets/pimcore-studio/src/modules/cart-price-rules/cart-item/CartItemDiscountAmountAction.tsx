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
import { Form, InputNumber, Checkbox } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const CartItemDiscountAmountAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const amount = data.amount || 0
  const gross = data.gross || false

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_action_discountAmount_amount', { defaultValue: 'Amount' })}>
        <InputNumber
          value={amount}
          onChange={(value) => handleChange('amount', value || 0)}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={gross}
          onChange={(e) => handleChange('gross', e.target.checked)}
        >
          {t('coreshop_prices_are_gross', { defaultValue: 'Prices are gross prices' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}
