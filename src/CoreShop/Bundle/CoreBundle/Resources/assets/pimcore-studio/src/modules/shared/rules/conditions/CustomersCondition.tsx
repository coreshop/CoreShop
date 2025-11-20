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

import React, { useEffect } from 'react'
import { Form } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import { CustomerMultiSelect } from '@coreshop/customer/src/components/CustomerMultiSelect'

export const CustomersCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useEffect(() => {
    form.setFieldsValue({ customers: data.customers })
  }, [data.customers, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange({ ...data, ...allValues })
      }}
    >
      <CustomerMultiSelect
        name="customers"
        label={t('coreshop_condition_customers', { defaultValue: 'Customers' })}
        value={data.customers}
        onChange={(ids) => onChange({ ...data, customers: ids })}
      />
    </Form>
  )
}
