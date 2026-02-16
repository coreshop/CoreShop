/**
 * CoreShop ShippingBundle - Shipping Rule Settings Form
 */

import React from 'react'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { ShippingRuleDetail } from '../api'

interface SettingsFormProps {
  rule: ShippingRuleDetail
  onChange: (rule: ShippingRuleDetail) => void
  currentLocale: string
  locales?: string[]
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  rule,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<ShippingRuleDetail>
        blockPrefix="coreshop_shipping_rule"
        data={rule}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
