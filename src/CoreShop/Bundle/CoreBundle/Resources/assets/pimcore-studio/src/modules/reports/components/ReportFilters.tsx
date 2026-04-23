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
import { Space, Button, DatePicker, Select, Divider, Tooltip } from 'antd'
import { SearchOutlined, DownloadOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import dayjs, { Dayjs } from 'dayjs'
import { StoreSelect } from '@coreshop/store/src/components/StoreSelect'
import type { GroupBy, ReportConfig } from '../types'

interface ReportFiltersProps {
  config: ReportConfig
  from: Dayjs
  to: Dayjs
  store?: number
  groupBy?: GroupBy
  onFromChange: (date: Dayjs) => void
  onToChange: (date: Dayjs) => void
  onStoreChange?: (store: number | undefined) => void
  onGroupByChange?: (groupBy: GroupBy) => void
  onFilter: () => void
  onExport: () => void
  loading?: boolean
}

/**
 * Quick date range presets
 */
interface DatePreset {
  key: string
  label: string
  getRange: () => [Dayjs, Dayjs]
}

/**
 * ReportFilters - Filter toolbar for reports
 */
export const ReportFilters: React.FC<ReportFiltersProps> = ({
  config,
  from,
  to,
  store,
  groupBy,
  onFromChange,
  onToChange,
  onStoreChange,
  onGroupByChange,
  onFilter,
  onExport,
  loading = false
}) => {
  const { t } = useTranslation()

  // Quick date presets
  const datePresets: DatePreset[] = [
    {
      key: 'day',
      label: t('coreshop_report_day', { defaultValue: 'Today' }),
      getRange: () => [dayjs().subtract(1, 'day'), dayjs()]
    },
    {
      key: 'month',
      label: t('coreshop_report_month', { defaultValue: 'This Month' }),
      getRange: () => [dayjs().startOf('month'), dayjs().endOf('month')]
    },
    {
      key: 'year',
      label: t('coreshop_report_year', { defaultValue: 'This Year' }),
      getRange: () => [dayjs().startOf('year'), dayjs().endOf('year')]
    },
    {
      key: 'day_minus',
      label: t('coreshop_report_day_minus', { defaultValue: 'Yesterday' }),
      getRange: () => [dayjs().subtract(2, 'day'), dayjs().subtract(1, 'day')]
    },
    {
      key: 'month_minus',
      label: t('coreshop_report_month_minus', { defaultValue: 'Last Month' }),
      getRange: () => {
        const lastMonth = dayjs().subtract(1, 'month')
        return [lastMonth.startOf('month'), lastMonth.endOf('month')]
      }
    },
    {
      key: 'year_minus',
      label: t('coreshop_report_year_minus', { defaultValue: 'Last Year' }),
      getRange: () => {
        const lastYear = dayjs().subtract(1, 'year')
        return [lastYear.startOf('year'), lastYear.endOf('year')]
      }
    }
  ]

  // Group by options
  const groupByOptions = [
    { value: 'day', label: t('coreshop_report_groups_day', { defaultValue: 'Day' }) },
    { value: 'month', label: t('coreshop_report_groups_month', { defaultValue: 'Month' }) },
    { value: 'year', label: t('coreshop_report_groups_year', { defaultValue: 'Year' }) }
  ]

  // Handle quick date selection
  const handleQuickDate = (preset: DatePreset) => {
    const [newFrom, newTo] = preset.getRange()
    onFromChange(newFrom)
    onToChange(newTo)
  }

  return (
    <div style={{ padding: '12px 0' }}>
      {/* Quick date buttons */}
      <Space wrap style={{ marginBottom: 12 }}>
        {datePresets.map(preset => (
          <Button
            key={preset.key}
            size="small"
            onClick={() => handleQuickDate(preset)}
          >
            {preset.label}
          </Button>
        ))}
      </Space>

      <Divider style={{ margin: '12px 0' }} />

      {/* Main filters */}
      <Space wrap>
        <DatePicker
          value={from}
          onChange={(date) => date && onFromChange(date)}
          placeholder={t('coreshop_report_year_from', { defaultValue: 'From' })}
          format="YYYY-MM-DD"
        />

        <DatePicker
          value={to}
          onChange={(date) => date && onToChange(date)}
          placeholder={t('coreshop_report_year_to', { defaultValue: 'To' })}
          format="YYYY-MM-DD"
        />

        {config.hasStoreFilter && onStoreChange && (
          <StoreSelect
            value={store}
            onChange={(value) => onStoreChange(value as number | undefined)}
            placeholder={t('coreshop_all_stores', { defaultValue: 'All Stores' })}
            allowClear
            style={{ width: 180 }}
          />
        )}

        {config.hasGroupBy && onGroupByChange && (
          <Select
            value={groupBy}
            onChange={onGroupByChange}
            options={groupByOptions}
            placeholder={t('coreshop_report_groups', { defaultValue: 'Group By' })}
            style={{ width: 120 }}
          />
        )}

        <Button
          type="primary"
          icon={<SearchOutlined />}
          onClick={onFilter}
          loading={loading}
        >
          {t('coreshop_report_filter', { defaultValue: 'Filter' })}
        </Button>

        <Tooltip title={t('coreshop_report_export', { defaultValue: 'Export' })}>
          <Button
            icon={<DownloadOutlined />}
            onClick={onExport}
          >
            {t('coreshop_report_export', { defaultValue: 'Export' })}
          </Button>
        </Tooltip>
      </Space>
    </div>
  )
}
