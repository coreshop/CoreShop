/**
 * CoreShop OrderBundle Sale Types
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export type SaleType = 'order' | 'cart' | 'quote'

export interface Customer {
  id: number
  name?: string
  email?: string
  company?: string
}

export interface SaleItem {
  id?: number
  productId?: number
  productName: string
  quantity: number
  unitPrice: number
  totalPrice: number
  totalTax?: number
  customItemDiscount?: number
  customItemPrice?: number
}

export interface Payment {
  id: number
  datePayment?: string
  amount: number
  state?: string
  provider?: string
  details?: Record<string, any>
}

export interface Shipment {
  id: number
  trackingCode?: string
  carrier?: string
  weight?: number
  state?: string
  dateShipped?: string
}

export interface Invoice {
  id: number
  invoiceNumber?: string
  invoiceDate?: string
  totalGross: number
  totalNet: number
  state?: string
}

export interface Comment {
  id: number
  comment: string
  date: string
  submitAsEmail?: boolean
}

export interface PriceRule {
  id: number
  name: string
  discount: number
}

export interface Currency {
  id: number
  name: string
  symbol: string
  isoCode: string
}

export interface Store {
  id: number
  name: string
}

export interface State {
  label: string
  state: string
  color: string
}

export interface Sale {
  id: number
  type: SaleType

  // Common fields
  customer?: Customer
  customerId?: number
  currency: Currency  // Can be string or Currency object
  store?: string | Store  // Can be string or Store object
  storeId?: number
  saleDate: string | number  // Can be string or timestamp
  saleLanguage?: string

  // Financial
  totalGross: number
  totalNet?: number
  totalTax?: number
  subtotalGross?: number
  subtotalNet?: number
  subtotalTax?: number
  shippingGross?: number
  shippingNet?: number
  shippingTax?: number
  discount?: number

  // Items & Rules
  items: SaleItem[]
  priceRules?: PriceRule[]

  // States & Editable
  editable: boolean

  // Order specific
  saleNumber?: string
  orderState?: string | State  // Can be string or State object
  paymentState?: string | State  // Can be string or State object
  shipmentState?: string | State  // Can be string or State object
  invoiceState?: string | State  // Can be string or State object
  payments?: Payment[]
  shipments?: Shipment[]
  invoices?: Invoice[]

  // Quote specific
  quoteNumber?: string

  // Comments
  comments?: Comment[]

  // Additional data
  shippingAddress?: any
  billingAddress?: any
  details?: Record<string, any>
  summary?: Record<string, any>
}

export interface StateList {
  orderStates: Array<{ key: string; label: string }>
  paymentStates: Array<{ key: string; label: string }>
  shipmentStates: Array<{ key: string; label: string }>
  invoiceStates: Array<{ key: string; label: string }>
}
