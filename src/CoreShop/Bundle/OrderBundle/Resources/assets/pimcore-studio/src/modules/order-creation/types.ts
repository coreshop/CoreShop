/**
 * CoreShop OrderBundle - Order Creation Types
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type React from 'react'

// ============================================
// API Request/Response Types
// ============================================

export interface OrderCreationItem {
  product: number
  quantity: number
  customItemPrice?: number
  customItemDiscount?: number
  unitDefinition?: number
}

export interface OrderCreationRequest {
  customer: number
  store: number
  currency: number
  localeCode: string
  saleType: 'cart' | 'order' | 'quote'
  items: OrderCreationItem[]
  // Cart name (only for carts)
  name?: string
  // Extended by CoreBundle
  shippingAddress?: number
  invoiceAddress?: number
  carrier?: number
  [key: string]: unknown
}

export interface AddressInfo {
  id: number
  firstname?: string
  lastname?: string
  company?: string
  street?: string
  number?: string
  postcode?: string
  city?: string
  country?: number
  countryName?: string
  phoneNumber?: string
}

export interface CustomerDetails {
  id: number
  email?: string
  firstname?: string
  lastname?: string
  addresses?: AddressInfo[]
}

export interface PreviewItem {
  product: number
  productName: string
  quantity: number
  price: number
  total: number
  convertedPrice: number
  convertedTotal: number
  customItemPrice: number
  customItemDiscount: number
  convertedCustomItemPrice: number
  unitDefinition: number | null
  unitDefinitionRecord: unknown | null
  units: unknown[]
}

export interface CurrencyInfo {
  id: number
  name: string
  symbol: string
  isoCode: string
}

export interface StoreInfo {
  id: number
  name: string
}

export interface SummaryItem {
  key: string
  value: number
  convertedValue: number
}

export interface CarrierInfo {
  id: number
  name: string
  price: number
}

export interface OrderCreationPreview {
  id: number | null
  customer: CustomerDetails | null
  items: PreviewItem[]
  currency: CurrencyInfo
  baseCurrency: CurrencyInfo
  store: StoreInfo
  summary: SummaryItem[]
  address: {
    shipping: AddressInfo | null
    billing: AddressInfo | null
  }
  address_shipping_formatted: string
  address_billing_formatted: string
  // CoreBundle extension
  carriers?: CarrierInfo[]
}

// ============================================
// State Management Types
// ============================================

export interface OrderCreationFormData {
  store: number | null
  currency: number | null
  localeCode: string | null
  items: OrderCreationItem[]
  // Extended by CoreBundle
  shippingAddress: number | null
  invoiceAddress: number | null
  carrier: number | null
  [key: string]: unknown
}

export interface OrderCreationState {
  // Customer (selected first, before wizard)
  customerId: number | null
  customerDetails: CustomerDetails | null

  // Form data
  formData: OrderCreationFormData

  // Preview state (from API)
  preview: OrderCreationPreview | null
  previewLoading: boolean
  previewError: string | null

  // Validation
  stepValidation: Record<string, boolean>

  // UI state
  creating: boolean
  createError: string | null
  resetKey: number
}

// ============================================
// Action Types
// ============================================

export type OrderCreationAction =
  | { type: 'SET_CUSTOMER'; payload: { id: number; details: CustomerDetails } }
  | { type: 'UPDATE_FORM_DATA'; payload: Partial<OrderCreationFormData> }
  | { type: 'SET_PREVIEW_LOADING'; payload: boolean }
  | { type: 'SET_PREVIEW'; payload: OrderCreationPreview }
  | { type: 'SET_PREVIEW_ERROR'; payload: string }
  | { type: 'SET_STEP_VALIDATION'; payload: { step: string; valid: boolean } }
  | { type: 'SET_CREATING'; payload: boolean }
  | { type: 'SET_CREATE_ERROR'; payload: string }
  | { type: 'RESET' }
  | { type: 'FULL_RESET' }

// ============================================
// Step Registry Types
// ============================================

export interface OrderCreationStepProps {
  state: OrderCreationState
  dispatch: React.Dispatch<OrderCreationAction>
  preview: OrderCreationPreview | null
  triggerPreview: () => void
}

export interface OrderCreationStepConfig {
  key: string
  label: string
  icon?: string
  priority: number

  // Component to render the step
  component: React.ComponentType<OrderCreationStepProps>

  // Validation function - determines if step is valid
  isValid: (state: OrderCreationState) => boolean

  // Get values from this step for preview/create API
  getValues: (state: OrderCreationState) => Record<string, unknown>

  // Optional: Condition for showing this step
  isVisible?: (state: OrderCreationState) => boolean

  // Optional: Receive preview data and update state
  onPreviewData?: (
    data: OrderCreationPreview,
    dispatch: React.Dispatch<OrderCreationAction>
  ) => void
}
