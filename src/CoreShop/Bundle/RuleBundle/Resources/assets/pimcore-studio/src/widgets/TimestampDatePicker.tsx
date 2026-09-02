/**
 * CoreShop RuleBundle - Timestamp DatePicker Widget
 *
 * Converts between millisecond timestamps (stored value) and dayjs objects (DatePicker value).
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
import { DatePicker } from 'antd'
import dayjs from 'dayjs'
import type { Dayjs } from 'dayjs'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import weekday from 'dayjs/plugin/weekday'
import localeData from 'dayjs/plugin/localeData'

dayjs.extend(customParseFormat)
dayjs.extend(weekday)
dayjs.extend(localeData)

interface TimestampDatePickerProps {
  value?: number | null
  onChange?: (value: number | null) => void
  style?: React.CSSProperties
}

export const TimestampDatePicker: React.FC<TimestampDatePickerProps> = ({ value, onChange, style }) => {
  const dayjsValue = value ? dayjs(value) : null

  const handleChange = (date: Dayjs | null) => {
    onChange?.(date ? date.valueOf() : null)
  }

  return (
    <DatePicker
      showTime
      value={dayjsValue}
      onChange={handleChange}
      style={style ?? { width: '100%' }}
    />
  )
}
