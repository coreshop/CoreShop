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
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'

interface StoreSettingsFormProps {
  storeId: string
  values: Record<string, any>
  onChange: (key: string, value: any) => void
}

/**
 * Mapping between flat field names (used by the FormType schema)
 * and dot-notation keys (used by the configuration store).
 */
const FIELD_TO_KEY: Record<string, string> = {
  guest_checkout: 'system.guest.checkout',
  category_list_mode: 'system.category.list.mode',
  category_list_per_page: 'system.category.list.per_page',
  category_list_per_page_default: 'system.category.list.per_page.default',
  category_list_include_subcategories: 'system.category.list.include_subcategories',
  category_grid_per_page: 'system.category.grid.per_page',
  category_grid_per_page_default: 'system.category.grid.per_page.default',
  category_variant_mode: 'system.category.variant_mode',
  quote_prefix: 'system.quote.prefix',
  quote_suffix: 'system.quote.suffix',
  order_prefix: 'system.order.prefix',
  order_suffix: 'system.order.suffix',
  invoice_prefix: 'system.invoice.prefix',
  invoice_suffix: 'system.invoice.suffix',
  invoice_wkhtml: 'system.invoice.wkhtml',
  shipment_prefix: 'system.shipment.prefix',
  shipment_suffix: 'system.shipment.suffix',
  shipment_wkhtml: 'system.shipment.wkhtml',
}

const KEY_TO_FIELD: Record<string, string> = Object.fromEntries(
  Object.entries(FIELD_TO_KEY).map(([field, key]) => [key, field])
)

/**
 * Convert dot-notation config values to flat field names for the schema form.
 */
const toFormData = (values: Record<string, any>): Record<string, any> => {
  const result: Record<string, any> = {}
  for (const [key, value] of Object.entries(values)) {
    const field = KEY_TO_FIELD[key]
    if (field) {
      // Convert per_page arrays to comma-separated strings for text fields
      if ((field === 'category_list_per_page' || field === 'category_grid_per_page') && Array.isArray(value)) {
        result[field] = value.join(', ')
      } else {
        result[field] = value
      }
    }
  }
  return result
}

/**
 * StoreSettingsForm - Form for a single store's configuration
 */
export const StoreSettingsForm: React.FC<StoreSettingsFormProps> = ({
  storeId,
  values,
  onChange
}) => {
  const formData = React.useMemo(() => toFormData(values), [values])

  const handleChange = (draft: Record<string, any>) => {
    for (const [field, value] of Object.entries(draft)) {
      const key = FIELD_TO_KEY[field]
      if (key) {
        // Convert comma-separated per_page strings back to arrays
        if ((field === 'category_list_per_page' || field === 'category_grid_per_page') && typeof value === 'string') {
          const parsed = value
            .split(',')
            .map(s => Number.parseInt(s.trim(), 10))
            .filter(n => !Number.isNaN(n) && n > 0)
            .sort((a, b) => a - b)
          onChange(key, parsed)
        } else {
          onChange(key, value)
        }
      }
    }
  }

  return (
    <div style={{ padding: '0 8px' }}>
      <SchemaForm
        blockPrefix="coreshop_store_settings"
        data={formData}
        onChange={handleChange}
      />
    </div>
  )
}
