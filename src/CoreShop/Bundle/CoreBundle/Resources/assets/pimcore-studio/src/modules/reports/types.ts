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

/**
 * Available report types
 */
export type ReportType =
  | 'sales'
  | 'products'
  | 'categories'
  | 'customers'
  | 'carriers'
  | 'payment_providers'
  | 'carts'
  | 'carts_abandoned'
  | 'vouchers'
  | 'manufacturer'

/**
 * Group by options for time-based reports
 */
export type GroupBy = 'day' | 'month' | 'year'

/**
 * Report filter parameters
 */
export interface ReportFilterParams {
  from: number  // Unix timestamp
  to: number    // Unix timestamp
  store?: number | number[]
  groupBy?: GroupBy
  [key: string]: any
}

/**
 * Report data item - generic structure
 */
export interface ReportDataItem {
  [key: string]: any
}

/**
 * Report API response
 */
export interface ReportResponse {
  success: boolean
  data: ReportDataItem[]
  total?: number
}

/**
 * Sales report data item
 */
export interface SalesReportItem {
  timestamp: number
  datetext: string
  sales: number
  salesFormatted: string
}

/**
 * Products report data item
 */
export interface ProductsReportItem {
  name: string
  productName: string
  orderCount: number
  quantityCount: number
  sales: number
  salesPrice: number
  profit: number
}

/**
 * Customers report data item
 */
export interface CustomersReportItem {
  name: string
  email: string
  orderCount: number
  sales: number
  salesFormatted: string
}

/**
 * Carriers report data item
 */
export interface CarriersReportItem {
  carrier: string
  count: number
  sales: number
  salesFormatted: string
}

/**
 * Payment providers report data item
 */
export interface PaymentProvidersReportItem {
  provider: string
  count: number
  sales: number
  salesFormatted: string
}

/**
 * Categories report data item
 */
export interface CategoriesReportItem {
  name: string
  categoryName: string
  orderCount: number
  quantityCount: number
  sales: number
  salesFormatted: string
  profit: number
  profitFormatted: string
}

/**
 * Vouchers report data item
 */
export interface VouchersReportItem {
  code: string
  discount: string
  rule: string
  usedDate: number
}

/**
 * Carts report data item
 */
export interface CartsReportItem {
  timestamp: number
  datetext: string
  carts: number
  orders: number
}

/**
 * Abandoned carts report data item
 */
export interface AbandonedCartsReportItem {
  cartId: number
  userName: string
  email: string
  selectedPayment: string
  creationDate: number
  modificationDate: number
  itemsInCart: number
}

/**
 * Manufacturer report data item
 */
export interface ManufacturerReportItem {
  name: string
  manufacturerName: string
  orderCount: number
  quantityCount: number
  sales: number
  salesFormatted: string
  profit: number
  profitFormatted: string
}

/**
 * Report configuration
 */
export interface ReportConfig {
  type: ReportType
  name: string
  icon: string
  hasStoreFilter?: boolean
  hasGroupBy?: boolean
  hasPagination?: boolean
}

/**
 * All available reports
 */
export const REPORT_CONFIGS: ReportConfig[] = [
  { type: 'sales', name: 'coreshop_report_sales', icon: 'LineChartOutlined', hasStoreFilter: true, hasGroupBy: true },
  { type: 'products', name: 'coreshop_report_products', icon: 'ShoppingOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'categories', name: 'coreshop_report_categories', icon: 'AppstoreOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'customers', name: 'coreshop_report_customers', icon: 'TeamOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'carriers', name: 'coreshop_report_carriers', icon: 'CarOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'payment_providers', name: 'coreshop_report_payment_providers', icon: 'CreditCardOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'carts', name: 'coreshop_report_carts', icon: 'ShoppingCartOutlined', hasStoreFilter: true, hasGroupBy: true },
  { type: 'carts_abandoned', name: 'coreshop_report_carts_abandoned', icon: 'StopOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'vouchers', name: 'coreshop_report_vouchers', icon: 'TagOutlined', hasStoreFilter: true, hasPagination: true },
  { type: 'manufacturer', name: 'coreshop_report_manufacturer', icon: 'BankOutlined', hasStoreFilter: true, hasPagination: true }
]
