/**
 * CoreShop PaymentBundle Studio Plugin
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
import { PaymentProviderRuleSelect } from '../components/PaymentProviderRuleSelect'

interface PaymentProviderRuleConditionConfig {
  paymentProviderRule?: number
}

export const PaymentProviderRuleCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()

  const handleChange = (value: number) => {
    onChange({ ...data, paymentProviderRule: value })
  }

  return (
    <Form.Item
      label={t('coreshop_condition_paymentProviderRule', { defaultValue: 'Payment Provider Rule' })}
    >
      <PaymentProviderRuleSelect
        value={data?.paymentProviderRule}
        onChange={handleChange}
        style={{ width: '100%' }}
      />
    </Form.Item>
  )
}
