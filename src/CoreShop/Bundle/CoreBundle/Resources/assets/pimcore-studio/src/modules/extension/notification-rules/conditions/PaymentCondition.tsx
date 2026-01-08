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
import { Form } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'
import { PaymentProviderSelect } from '@coreshop/payment/src/components/PaymentProviderSelect'

interface PaymentConditionConfig {
  paymentProvider?: number
}

/**
 * Payment Provider condition for notification rules - for order-related notifications
 */
export const PaymentCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()

  const handleChange = (value: number) => {
    onChange({ ...data, paymentProvider: value })
  }

  return (
    <Form.Item label={t('coreshop_payment_provider', { defaultValue: 'Payment Provider' })}>
      <PaymentProviderSelect
        value={data?.paymentProvider}
        onChange={handleChange}
        placeholder={t('coreshop_select_payment_provider', { defaultValue: 'Select a payment provider' })}
      />
    </Form.Item>
  )
}
