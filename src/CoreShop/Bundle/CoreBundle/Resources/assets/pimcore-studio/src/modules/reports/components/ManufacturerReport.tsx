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

const manufacturerConfig: ReportConfig = {
  type: 'manufacturer',
  name: 'coreshop_report_manufacturer',
  icon: 'BankOutlined',
  hasStoreFilter: true,
  hasPagination: true
}

/**
 * ManufacturerReport - Table showing manufacturer performance
 */
export const ManufacturerReport: React.FC = () => {
  const { t } = useTranslation()

  const columns = [
    {
      title: t('name', { defaultValue: 'Name' }),
      dataIndex: 'name',
      key: 'name',
      sorter: true
    },
    {
      title: t('coreshop_report_products_order_count', { defaultValue: 'Orders' }),
      dataIndex: 'orderCount',
      key: 'orderCount',
      align: 'right' as const,
      sorter: true
    },
    {
      title: t('coreshop_report_products_quantity_count', { defaultValue: 'Quantity' }),
      dataIndex: 'quantityCount',
      key: 'quantityCount',
      align: 'right' as const,
      sorter: true
    },
    {
      title: t('coreshop_report_manufacturer_sales', { defaultValue: 'Sales' }),
      dataIndex: 'salesFormatted',
      key: 'salesFormatted',
      align: 'right' as const
    },
    {
      title: t('coreshop_report_manufacturer_profit', { defaultValue: 'Profit' }),
      dataIndex: 'profitFormatted',
      key: 'profitFormatted',
      align: 'right' as const
    }
  ]

  return (
    <ReportPanel
      config={manufacturerConfig}
      columns={columns}
    />
  )
}
