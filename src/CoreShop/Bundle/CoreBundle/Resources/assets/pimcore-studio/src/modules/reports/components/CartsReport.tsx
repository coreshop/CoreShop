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
import type { ReportConfig, CartsReportItem, ReportDataItem } from '../types'

const cartsConfig: ReportConfig = {
  type: 'carts',
  name: 'coreshop_report_carts',
  icon: 'ShoppingCartOutlined',
  hasStoreFilter: true,
  hasGroupBy: true
}

/**
 * CartsReport - Dual line chart showing carts vs orders over time
 */
export const CartsReport: React.FC = () => {
  const { t } = useTranslation()

  const renderChart = (data: ReportDataItem[]) => {
    const items = data as CartsReportItem[]

    if (items.length === 0) {
      return null
    }

    // Transform data for dual-line chart
    const chartData = items.flatMap(item => [
      { date: item.datetext, value: item.carts, type: t('coreshop_cart', { defaultValue: 'Carts' }) },
      { date: item.datetext, value: item.orders, type: t('coreshop_order', { defaultValue: 'Orders' }) }
    ])

    const config = {
      data: chartData,
      xField: 'date',
      yField: 'value',
      seriesField: 'type',
      smooth: true,
      point: {
        size: 4,
        shape: 'circle'
      },
      color: ['#01841c', '#15428B'],
      xAxis: {
        title: { text: t('coreshop_report_date', { defaultValue: 'Date' }) }
      },
      yAxis: {
        title: { text: t('coreshop_report_count', { defaultValue: 'Count' }) },
        min: 0
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
      title: t('coreshop_cart', { defaultValue: 'Carts' }),
      dataIndex: 'carts',
      key: 'carts',
      align: 'right' as const
    },
    {
      title: t('coreshop_order', { defaultValue: 'Orders' }),
      dataIndex: 'orders',
      key: 'orders',
      align: 'right' as const
    }
  ]

  return (
    <ReportPanel
      config={cartsConfig}
      columns={columns}
      renderChart={renderChart}
    />
  )
}
