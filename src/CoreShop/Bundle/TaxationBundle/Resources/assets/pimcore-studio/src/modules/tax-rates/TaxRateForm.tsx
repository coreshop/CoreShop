/**
 * CoreShop TaxationBundle Studio Plugin
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
import type { TaxRateDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export interface TaxRateFormProps {
  data?: TaxRateDetail
  onChange: (draft: Partial<TaxRateDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const TaxRateForm: React.FC<TaxRateFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<TaxRateDetail>
        alias="coreshop.tax_rate"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />

      {renderEntityFormExtensions('coreshop.taxation.tax_rate.form', { data, onChange, currentLocale })}
    </div>
  )
}
