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
import { SchemaForm, type SchemaFormProps } from '@coreshop/studio-form/src/schema-adapter'
import type { TaxRateDetail } from './api'

export const TaxRateForm: React.FC<Omit<SchemaFormProps<TaxRateDetail>, 'blockPrefix'>> = (props) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<TaxRateDetail>
        {...props}
        blockPrefix="coreshop_tax_rate"
      />
    </div>
  )
}
