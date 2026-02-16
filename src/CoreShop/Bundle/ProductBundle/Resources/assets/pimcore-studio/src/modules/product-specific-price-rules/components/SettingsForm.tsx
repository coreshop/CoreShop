/**
 * CoreShop ProductBundle Studio Plugin
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
import type { ProductSpecificPriceRule } from '../types'

interface SettingsFormProps {
  rule: ProductSpecificPriceRule
  onChange: (rule: ProductSpecificPriceRule) => void
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
      <SchemaForm<ProductSpecificPriceRule>
        blockPrefix="coreshop_product_specific_price_rule"
        data={rule}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
