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
import { Form, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const NotDiscountableCustomAttributesAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const attributes = data.attributes || ''

  const handleChange = (value: string) => {
    onChange({ ...data, attributes: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_custom_attributes', { defaultValue: 'Custom Attributes' })}>
        <Input.TextArea
          value={attributes}
          onChange={(e) => handleChange(e.target.value)}
          rows={3}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
