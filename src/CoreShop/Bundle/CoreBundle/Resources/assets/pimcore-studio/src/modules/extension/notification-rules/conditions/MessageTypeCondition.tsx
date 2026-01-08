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
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

interface MessageTypeConditionConfig {
  messageType?: string
}

/**
 * Message Type condition for messaging notification rules
 */
export const MessageTypeCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()

  const handleChange = (value: string) => {
    onChange({ ...data, messageType: value })
  }

  const options = [
    { value: 'customer', label: t('coreshop_message_type_customer', { defaultValue: 'Customer' }) },
    { value: 'customer-reply', label: t('coreshop_message_type_customer_reply', { defaultValue: 'Customer Reply' }) },
    { value: 'contact', label: t('coreshop_message_type_contact', { defaultValue: 'Contact' }) }
  ]

  return (
    <Form.Item label={t('coreshop_message_type', { defaultValue: 'Message Type' })}>
      <Select
        value={data?.messageType}
        onChange={handleChange}
        options={options}
        placeholder={t('coreshop_select_message_type', { defaultValue: 'Select a message type' })}
      />
    </Form.Item>
  )
}
