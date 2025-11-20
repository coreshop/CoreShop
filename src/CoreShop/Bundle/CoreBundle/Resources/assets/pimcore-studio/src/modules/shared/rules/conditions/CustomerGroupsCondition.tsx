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
import { CustomerGroupMultiSelect } from '@coreshop/customer/src/components/CustomerGroupMultiSelect'

export const CustomerGroupsCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useEffect(() => {
    form.setFieldsValue({ customerGroups: data.customerGroups })
  }, [data.customerGroups, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange({ ...data, ...allValues })
      }}
    >
      <CustomerGroupMultiSelect
        name="customerGroups"
        label={t('coreshop_condition_customerGroups', { defaultValue: 'Customer Groups' })}
        value={data.customerGroups}
        onChange={(ids) => onChange({ ...data, customerGroups: ids })}
      />
    </Form>
  )
}
