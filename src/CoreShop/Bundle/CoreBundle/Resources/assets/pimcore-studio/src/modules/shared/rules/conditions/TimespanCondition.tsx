/**
 * CoreShop OrderBundle Studio Plugin
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
import { Form, DatePicker } from 'antd'
import { useTranslation } from 'react-i18next'
import dayjs from 'dayjs'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const TimespanCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const dateFrom = data.dateFrom ? dayjs(data.dateFrom) : null
  const dateTo = data.dateTo ? dayjs(data.dateTo) : null

  const handleChange = (field: string, value: dayjs.Dayjs | null) => {
    onChange({ ...data, [field]: value ? value.valueOf() : null })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_report_year_from', { defaultValue: 'From' })}>
        <DatePicker
          showTime
          value={dateFrom}
          onChange={(value) => handleChange('dateFrom', value)}
          style={{ width: '100%' }}
          format="YYYY-MM-DD HH:mm"
        />
      </Form.Item>

      <Form.Item label={t('coreshop_report_year_to', { defaultValue: 'To' })}>
        <DatePicker
          showTime
          value={dateTo}
          onChange={(value) => handleChange('dateTo', value)}
          style={{ width: '100%' }}
          format="YYYY-MM-DD HH:mm"
        />
      </Form.Item>
    </Form>
  )
}
