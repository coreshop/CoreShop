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

interface CommentConditionConfig {
  commentAction?: string
}

/**
 * Comment condition for order notification rules
 * Triggers on comment actions (add comment, etc.)
 */
export const CommentCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()

  const handleChange = (value: string) => {
    onChange({ ...data, commentAction: value })
  }

  const options = [
    { value: 'add', label: t('coreshop_comment_action_add', { defaultValue: 'Add Comment' }) }
  ]

  return (
    <Form.Item label={t('coreshop_comment_action', { defaultValue: 'Comment Action' })}>
      <Select
        value={data?.commentAction}
        onChange={handleChange}
        options={options}
        placeholder={t('coreshop_select_comment_action', { defaultValue: 'Select a comment action' })}
      />
    </Form.Item>
  )
}
