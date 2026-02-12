/**
 * CoreShop StoreBundle Studio Plugin
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
import type { StoreDetail } from './api'

export interface StoreFormProps {
  data?: StoreDetail
  onChange: (draft: Partial<StoreDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const StoreForm: React.FC<StoreFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<StoreDetail>
        alias="coreshop.store"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
