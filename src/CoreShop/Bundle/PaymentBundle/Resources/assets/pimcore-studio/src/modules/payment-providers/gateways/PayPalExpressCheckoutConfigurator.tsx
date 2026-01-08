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
import { Form, Input, Checkbox } from 'antd'
import { useTranslation } from 'react-i18next'
import type { GatewayConfiguratorProps } from './GatewayRegistry'

export const PayPalExpressCheckoutConfigurator: React.FC<GatewayConfiguratorProps> = ({
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
        label={t('coreshop_username', { defaultValue: 'Username' })}
        name="username"
        rules={[{ required: true, message: t('coreshop_username_required', { defaultValue: 'Username is required' }) }]}
      >
        <Input />
      </Form.Item>

      <Form.Item
        label={t('coreshop_password', { defaultValue: 'Password' })}
        name="password"
        rules={[{ required: true, message: t('coreshop_password_required', { defaultValue: 'Password is required' }) }]}
      >
        <Input.Password />
      </Form.Item>

      <Form.Item
        label={t('coreshop_signature', { defaultValue: 'Signature' })}
        name="signature"
        rules={[{ required: true, message: t('coreshop_signature_required', { defaultValue: 'Signature is required' }) }]}
      >
        <Input />
      </Form.Item>

      <Form.Item
        name="sandbox"
        valuePropName="checked"
      >
        <Checkbox>
          {t('coreshop_paypal_sandbox', { defaultValue: 'Sandbox Mode' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}
