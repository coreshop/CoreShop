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
import type { ReportConfig, PaymentProvidersReportItem, ReportDataItem } from '../types'

const paymentProvidersConfig: ReportConfig = {
  type: 'payment_providers',
  name: 'coreshop_report_payment_providers',
  icon: 'CreditCardOutlined',
  hasStoreFilter: true
}

/**
 * PaymentProvidersReport - Pie chart showing payment provider distribution
 */
export const PaymentProvidersReport: React.FC = () => {
  const { t } = useTranslation()

  const renderChart = (data: ReportDataItem[]) => {
    const items = data as PaymentProvidersReportItem[]

    if (items.length === 0) {
      return null
    }

    const config = {
      data: items,
      angleField: 'count',
      colorField: 'provider',
      radius: 0.8,
      innerRadius: 0.5,
      label: {
        text: 'provider',
        position: 'outside' as const
      },
      tooltip: {
        title: 'provider'
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
      title: t('coreshop_payment_provider', { defaultValue: 'Payment Provider' }),
      dataIndex: 'provider',
      key: 'provider'
    },
    {
      title: t('coreshop_report_payment_providers_count', { defaultValue: 'Orders' }),
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
      config={paymentProvidersConfig}
      columns={columns}
      renderChart={renderChart}
    />
  )
}
