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
import { CartsReport } from './CartsReport'
import { CarriersReport } from './CarriersReport'
import { PaymentProvidersReport } from './PaymentProvidersReport'
import { CategoriesReport } from './CategoriesReport'
import { ManufacturerReport } from './ManufacturerReport'
import { VouchersReport } from './VouchersReport'
import { AbandonedCartsReport } from './AbandonedCartsReport'

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
      key: 'categories',
      label: (
        <span>
          <AppstoreOutlined />
          {t('coreshop_report_categories', { defaultValue: 'Categories' })}
        </span>
      ),
      children: <CategoriesReport />
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
    {
      key: 'carriers',
      label: (
        <span>
          <CarOutlined />
          {t('coreshop_report_carriers', { defaultValue: 'Carriers' })}
        </span>
      ),
      children: <CarriersReport />
    },
    {
      key: 'payment_providers',
      label: (
        <span>
          <CreditCardOutlined />
          {t('coreshop_report_payment_providers', { defaultValue: 'Payment Providers' })}
        </span>
      ),
      children: <PaymentProvidersReport />
    },
    {
      key: 'carts',
      label: (
        <span>
          <ShoppingCartOutlined />
          {t('coreshop_report_carts', { defaultValue: 'Carts' })}
        </span>
      ),
      children: <CartsReport />
    },
    {
      key: 'carts_abandoned',
      label: (
        <span>
          <StopOutlined />
          {t('coreshop_report_carts_abandoned', { defaultValue: 'Abandoned Carts' })}
        </span>
      ),
      children: <AbandonedCartsReport />
    },
    {
      key: 'vouchers',
      label: (
        <span>
          <TagOutlined />
          {t('coreshop_report_vouchers', { defaultValue: 'Vouchers' })}
        </span>
      ),
      children: <VouchersReport />
    },
    {
      key: 'manufacturer',
      label: (
        <span>
          <BankOutlined />
          {t('coreshop_report_manufacturer', { defaultValue: 'Manufacturer' })}
        </span>
      ),
      children: <ManufacturerReport />
    }
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
