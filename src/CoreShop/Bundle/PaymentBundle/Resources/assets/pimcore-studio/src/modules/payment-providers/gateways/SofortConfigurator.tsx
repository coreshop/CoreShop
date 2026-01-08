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
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { GatewayConfiguratorProps } from './GatewayRegistry'

export const SofortConfigurator: React.FC<GatewayConfiguratorProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  React.useEffect(() => {
    form.setFieldsValue(config)
  }, [config, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label={t('coreshop_payment_sofort_config_key', { defaultValue: 'Config Key' })}
        name="config_key"
        rules={[{ required: true, message: t('coreshop_config_key_required', { defaultValue: 'Config key is required' }) }]}
      >
        <Input />
      </Form.Item>
    </Form>
  )
}
