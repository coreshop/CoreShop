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

const productsConfig: ReportConfig = {
  type: 'products',
  name: 'coreshop_report_products',
  icon: 'ShoppingOutlined',
  hasStoreFilter: true,
  hasPagination: true
}

/**
 * ProductsReport - Table showing product performance
 */
export const ProductsReport: React.FC = () => {
  const { t } = useTranslation()

  const columns = [
    {
      title: t('coreshop_report_products_name', { defaultValue: 'Product Name' }),
      dataIndex: 'productName',
      key: 'productName',
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
      title: t('coreshop_report_products_quantity', { defaultValue: 'Quantity Sold' }),
      dataIndex: 'quantityCount',
      key: 'quantityCount',
      align: 'right' as const,
      sorter: true
    },
    {
      title: t('coreshop_sales', { defaultValue: 'Sales' }),
      dataIndex: 'sales',
      key: 'sales',
      align: 'right' as const,
      sorter: true,
      render: (value: number) => (value / 100).toFixed(2)
    },
    {
      title: t('coreshop_report_products_profit', { defaultValue: 'Profit' }),
      dataIndex: 'profit',
      key: 'profit',
      align: 'right' as const,
      sorter: true,
      render: (value: number) => (value / 100).toFixed(2)
    }
  ]

  return (
    <ReportPanel
      config={productsConfig}
      columns={columns}
    />
  )
}
