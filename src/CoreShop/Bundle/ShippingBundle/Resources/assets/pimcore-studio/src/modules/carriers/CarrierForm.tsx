/**
 * CoreShop ShippingBundle Studio Plugin
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
import type { CarrierDetail, CarrierConfig } from './api'

export interface CarrierFormProps {
  data?: CarrierDetail
  config: CarrierConfig
  onChange: (draft: Partial<CarrierDetail>) => void
  currentLocale: string
  locales?: string[]
}

export const CarrierForm: React.FC<CarrierFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 24 }}>
      <SchemaForm<CarrierDetail>
        blockPrefix="coreshop_carrier"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
