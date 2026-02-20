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
import { SchemaForm, type SchemaFormProps } from '@coreshop/studio-form/src/schema-adapter'
import type { PaymentProvider } from './api'

export const PaymentProviderForm: React.FC<Omit<SchemaFormProps<PaymentProvider>, 'blockPrefix'>> = (props) => {
  return (
    <div style={{ padding: 24 }}>
      <SchemaForm<PaymentProvider>
        {...props}
        blockPrefix="coreshop_payment_provider"
      />
    </div>
  )
}
