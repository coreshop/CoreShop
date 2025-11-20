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
import { CarrierMultiSelect } from '@coreshop/shipping/src/components/CarrierMultiSelect'

export const CarriersCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useEffect(() => {
    // Convert string IDs to numbers if needed (backend returns strings)
    const carriers = Array.isArray(data.carriers)
      ? data.carriers.map(id => typeof id === 'string' ? parseInt(id, 10) : id)
      : []
    form.setFieldsValue({ carriers })
  }, [data.carriers, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange({ ...data, ...allValues })
      }}
    >
      <CarrierMultiSelect
        name="carriers"
        label={t('coreshop_condition_carriers', { defaultValue: 'Carriers' })}
      />
    </Form>
  )
}
