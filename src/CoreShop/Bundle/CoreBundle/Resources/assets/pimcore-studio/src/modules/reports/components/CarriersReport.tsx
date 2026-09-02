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
import { Pie } from '@ant-design/plots'
import { useTranslation } from 'react-i18next'
import { ReportPanel } from './ReportPanel'
import type { ReportConfig, CarriersReportItem, ReportDataItem } from '../types'

const carriersConfig: ReportConfig = {
  type: 'carriers',
  name: 'coreshop_report_carriers',
  icon: 'CarOutlined',
  hasStoreFilter: true
}

/**
 * CarriersReport - Pie chart showing carrier distribution
 */
export const CarriersReport: React.FC = () => {
  const { t } = useTranslation()

  const renderChart = (data: ReportDataItem[]) => {
    const items = data as CarriersReportItem[]

    if (items.length === 0) {
      return null
    }

    const config = {
      data: items,
      angleField: 'count',
      colorField: 'carrier',
      radius: 0.8,
      innerRadius: 0.5,
      label: {
        text: 'carrier',
        position: 'outside' as const
      },
      tooltip: {
        title: 'carrier'
      },
      legend: {
        position: 'bottom' as const
      }
    }

    return (
      <div style={{ height: 400 }}>
        <Pie {...config} />
      </div>
    )
  }

  // Table columns (fallback)
  const columns = [
    {
      title: t('coreshop_carrier', { defaultValue: 'Carrier' }),
      dataIndex: 'carrier',
      key: 'carrier'
    },
    {
      title: t('coreshop_report_carriers_count', { defaultValue: 'Orders' }),
      dataIndex: 'count',
      key: 'count',
      align: 'right' as const
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
      config={carriersConfig}
      columns={columns}
      renderChart={renderChart}
    />
  )
}
