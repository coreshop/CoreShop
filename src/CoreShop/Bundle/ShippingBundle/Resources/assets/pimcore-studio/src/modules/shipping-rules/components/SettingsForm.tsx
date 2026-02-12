/**
 * CoreShop ShippingBundle - Shipping Rule Settings Form
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicForm, type FormBuilder } from '@coreshop/studio-form/src/form-builder'
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
  locales
}) => {
  const builder = container.get<FormBuilder<ShippingRuleDetail>>('CoreShop/Shipping/ShippingRule/FormBuilder')
  const config = React.useMemo(() => builder.build({ data: rule, locale: currentLocale, locales }), [builder, rule, currentLocale, locales])

  return (
    <div style={{ padding: 12 }}>
      <DynamicForm config={config} data={rule} onChange={onChange} currentLocale={currentLocale} />
    </div>
  )
}
