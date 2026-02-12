/**
 * CoreShop CurrencyBundle - Currency Form (Schema-driven)
 *
 * Form component using the StudioForm schema system.
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
import type { CurrencyDetail } from './api'

export interface CurrencyFormProps {
  data?: CurrencyDetail
  onChange: (draft: Partial<CurrencyDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const CurrencyForm: React.FC<CurrencyFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<CurrencyDetail>
        alias="coreshop.currency"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
