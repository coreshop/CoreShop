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
import { Tabs, Card } from 'antd'
import {
  LineChartOutlined,
  ShoppingOutlined,
  AppstoreOutlined,
  TeamOutlined,
  CarOutlined,
  CreditCardOutlined,
  ShoppingCartOutlined,
  StopOutlined,
  TagOutlined,
  BankOutlined
} from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { SalesReport } from './SalesReport'
import { ProductsReport } from './ProductsReport'
import { CustomersReport } from './CustomersReport'
import { ReportPanel } from './ReportPanel'
import type { ReportConfig } from '../types'

// Simple reports that use table view
const simpleReportConfigs: Record<string, { config: ReportConfig, columns: any[] }> = {
  carriers: {
    config: {
      type: 'carriers',
      name: 'coreshop_report_carriers',
      icon: 'CarOutlined',
      hasStoreFilter: true,
      hasPagination: true
    },
    columns: [
      { title: 'Carrier', dataIndex: 'carrier', key: 'carrier' },
      { title: 'Orders', dataIndex: 'count', key: 'count', align: 'right' },
      { title: 'Sales', dataIndex: 'salesFormatted', key: 'salesFormatted', align: 'right' }
    ]
  },
  payment_providers: {
    config: {
      type: 'payment_providers',
      name: 'coreshop_report_payment_providers',
      icon: 'CreditCardOutlined',
      hasStoreFilter: true,
      hasPagination: true
    },
    columns: [
      { title: 'Payment Provider', dataIndex: 'provider', key: 'provider' },
      { title: 'Orders', dataIndex: 'count', key: 'count', align: 'right' },
      { title: 'Sales', dataIndex: 'salesFormatted', key: 'salesFormatted', align: 'right' }
    ]
  },
  categories: {
    config: {
      type: 'categories',
      name: 'coreshop_report_categories',
      icon: 'AppstoreOutlined',
      hasStoreFilter: true,
      hasPagination: true
    },
    columns: [
      { title: 'Category', dataIndex: 'categoryName', key: 'categoryName' },
      { title: 'Orders', dataIndex: 'orderCount', key: 'orderCount', align: 'right' },
      { title: 'Quantity', dataIndex: 'quantityCount', key: 'quantityCount', align: 'right' },
      { title: 'Sales', dataIndex: 'sales', key: 'sales', align: 'right', render: (v: number) => (v / 100).toFixed(2) }
    ]
  },
  vouchers: {
    config: {
      type: 'vouchers',
      name: 'coreshop_report_vouchers',
      icon: 'TagOutlined',
      hasStoreFilter: true,
      hasPagination: true
    },
    columns: [
      { title: 'Code', dataIndex: 'code', key: 'code' },
      { title: 'Usage', dataIndex: 'usageCount', key: 'usageCount', align: 'right' },
      { title: 'Sales', dataIndex: 'salesFormatted', key: 'salesFormatted', align: 'right' }
    ]
  },
  manufacturer: {
    config: {
      type: 'manufacturer',
      name: 'coreshop_report_manufacturer',
      icon: 'BankOutlined',
      hasStoreFilter: true,
      hasPagination: true
    },
    columns: [
      { title: 'Manufacturer', dataIndex: 'name', key: 'name' },
      { title: 'Orders', dataIndex: 'orderCount', key: 'orderCount', align: 'right' },
      { title: 'Quantity', dataIndex: 'quantityCount', key: 'quantityCount', align: 'right' },
      { title: 'Sales', dataIndex: 'sales', key: 'sales', align: 'right', render: (v: number) => (v / 100).toFixed(2) }
    ]
  },
  carts_abandoned: {
    config: {
      type: 'carts_abandoned',
      name: 'coreshop_report_carts_abandoned',
      icon: 'StopOutlined',
      hasStoreFilter: true,
      hasPagination: true
    },
    columns: [
      { title: 'Cart ID', dataIndex: 'cartId', key: 'cartId' },
      { title: 'Created', dataIndex: 'createdDate', key: 'createdDate' },
      { title: 'Customer', dataIndex: 'email', key: 'email' },
      { title: 'Items', dataIndex: 'items', key: 'items', align: 'right' },
      { title: 'Total', dataIndex: 'totalFormatted', key: 'totalFormatted', align: 'right' }
    ]
  }
}

/**
 * Icon mapping
 */
const iconMap: Record<string, React.ReactNode> = {
  LineChartOutlined: <LineChartOutlined />,
  ShoppingOutlined: <ShoppingOutlined />,
  AppstoreOutlined: <AppstoreOutlined />,
  TeamOutlined: <TeamOutlined />,
  CarOutlined: <CarOutlined />,
  CreditCardOutlined: <CreditCardOutlined />,
  ShoppingCartOutlined: <ShoppingCartOutlined />,
  StopOutlined: <StopOutlined />,
  TagOutlined: <TagOutlined />,
  BankOutlined: <BankOutlined />
}

/**
 * ReportsManager - Main reports dashboard with tabs for each report type
 */
export const ReportsManager: React.FC = () => {
  const { t } = useTranslation()

  const tabItems = [
    {
      key: 'sales',
      label: (
        <span>
          <LineChartOutlined />
          {t('coreshop_report_sales', { defaultValue: 'Sales' })}
        </span>
      ),
      children: <SalesReport />
    },
    {
      key: 'products',
      label: (
        <span>
          <ShoppingOutlined />
          {t('coreshop_report_products', { defaultValue: 'Products' })}
        </span>
      ),
      children: <ProductsReport />
    },
    {
      key: 'customers',
      label: (
        <span>
          <TeamOutlined />
          {t('coreshop_report_customers', { defaultValue: 'Customers' })}
        </span>
      ),
      children: <CustomersReport />
    },
    // Add simple reports
    ...Object.entries(simpleReportConfigs).map(([key, { config, columns }]) => ({
      key,
      label: (
        <span>
          {iconMap[config.icon]}
          {t(config.name, { defaultValue: config.name })}
        </span>
      ),
      children: <ReportPanel config={config} columns={columns} />
    }))
  ]

  return (
    <Card title={t('coreshop_reports', { defaultValue: 'Reports' })} style={{ height: '100%' }}>
      <Tabs
        defaultActiveKey="sales"
        items={tabItems}
        tabPosition="left"
        style={{ minHeight: 500 }}
      />
    </Card>
  )
}
