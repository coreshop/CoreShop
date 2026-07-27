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
import { Line } from '@ant-design/plots'
import { useTranslation } from 'react-i18next'
import { ReportPanel } from './ReportPanel'
import type { ReportConfig, SalesReportItem, ReportDataItem } from '../types'

const salesConfig: ReportConfig = {
  type: 'sales',
  name: 'coreshop_report_sales',
  icon: 'LineChartOutlined',
  hasStoreFilter: true,
  hasGroupBy: true
}

/**
 * SalesReport - Line chart showing sales over time
 */
export const SalesReport: React.FC = () => {
  const { t } = useTranslation()

  // Render chart
  const renderChart = (data: ReportDataItem[]) => {
    const chartData = (data as SalesReportItem[]).map(item => ({
      date: item.datetext,
      sales: item.sales / 100, // Convert from cents
      salesFormatted: item.salesFormatted
    }))

    if (chartData.length === 0) {
      return null
    }

    const config = {
      data: chartData,
      xField: 'date',
      yField: 'sales',
      smooth: true,
      point: {
        size: 4,
        shape: 'circle'
      },
      tooltip: {
        formatter: (datum: any) => ({
          name: t('coreshop_sales', { defaultValue: 'Sales' }),
          value: datum.salesFormatted ?? `${datum.sales.toFixed(2)}`
        })
      },
      color: '#01841c',
      xAxis: {
        title: { text: t('coreshop_report_date', { defaultValue: 'Date' }) }
      },
      yAxis: {
        title: { text: t('coreshop_sales', { defaultValue: 'Sales' }) }
      }
    }

    return (
      <div style={{ height: 300 }}>
        <Line {...config} />
      </div>
    )
  }

  // Table columns (fallback if chart fails)
  const columns = [
    {
      title: t('coreshop_report_date', { defaultValue: 'Date' }),
      dataIndex: 'datetext',
      key: 'datetext'
    },
    {
      title: t('coreshop_sales', { defaultValue: 'Sales' }),
      dataIndex: 'salesFormatted',
      key: 'salesFormatted',
      align: 'right' as const
    }
  ]

  return (
    <ReportPanel
      config={salesConfig}
      columns={columns}
      renderChart={renderChart}
    />
  )
}
