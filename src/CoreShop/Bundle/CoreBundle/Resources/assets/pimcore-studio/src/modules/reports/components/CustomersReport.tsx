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
import { useTranslation } from 'react-i18next'
import { ReportPanel } from './ReportPanel'
import type { ReportConfig } from '../types'

const customersConfig: ReportConfig = {
  type: 'customers',
  name: 'coreshop_report_customers',
  icon: 'TeamOutlined',
  hasStoreFilter: true,
  hasPagination: true
}

/**
 * CustomersReport - Table showing customer performance
 */
export const CustomersReport: React.FC = () => {
  const { t } = useTranslation()

  const columns = [
    {
      title: t('coreshop_report_customers_name', { defaultValue: 'Name' }),
      dataIndex: 'name',
      key: 'name',
      sorter: true
    },
    {
      title: t('coreshop_report_customers_email', { defaultValue: 'Email' }),
      dataIndex: 'email',
      key: 'email'
    },
    {
      title: t('coreshop_report_customers_order_count', { defaultValue: 'Orders' }),
      dataIndex: 'orderCount',
      key: 'orderCount',
      align: 'right' as const,
      sorter: true
    },
    {
      title: t('coreshop_sales', { defaultValue: 'Total Sales' }),
      dataIndex: 'salesFormatted',
      key: 'salesFormatted',
      align: 'right' as const
    }
  ]

  return (
    <ReportPanel
      config={customersConfig}
      columns={columns}
    />
  )
}
