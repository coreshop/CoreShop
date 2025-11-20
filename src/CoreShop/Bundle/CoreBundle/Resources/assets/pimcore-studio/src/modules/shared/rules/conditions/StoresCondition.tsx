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
import { StoreMultiSelect } from '@coreshop/store/src/components/StoreMultiSelect'

export const StoresCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useEffect(() => {
    // Convert string IDs to numbers if needed (backend returns strings)
    const stores = Array.isArray(data.stores)
      ? data.stores.map(id => typeof id === 'string' ? parseInt(id, 10) : id)
      : []
    form.setFieldsValue({ stores })
  }, [data.stores, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange({ ...data, ...allValues })
      }}
    >
      <StoreMultiSelect
        name="stores"
        label={t('coreshop_condition_stores', { defaultValue: 'Stores' })}
      />
    </Form>
  )
}
