/**
 * CoreShop PaymentBundle Studio Plugin
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
import type { PaymentProviderRule } from '../types'

interface SettingsFormProps {
  rule: PaymentProviderRule
  onChange: (rule: PaymentProviderRule) => void
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
      <SchemaForm<PaymentProviderRule>
        blockPrefix="coreshop_payment_provider_rule"
        data={rule}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
