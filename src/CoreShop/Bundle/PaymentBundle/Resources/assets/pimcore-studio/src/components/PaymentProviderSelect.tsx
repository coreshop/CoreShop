/**
 * CoreShop PaymentBundle - Payment Provider Select
 *
 * Select component for choosing payment providers with module-level caching.
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
import type { SelectProps } from 'antd'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { EntitySelect } from '@coreshop/resource/src/components/EntitySelect'
import { paymentProviderApi } from '../modules/payment-providers/api'

const { load: loadPaymentProviders, getCache: getPaymentProviderCache, clearCache: clearPaymentProviderCache } = createOptionsLoader(async () => {
  const providers = await paymentProviderApi.list()
  return providers.map(provider => ({
    value: provider.id!,
    label: provider.identifier ?? `#${provider.id}`
  }))
})

export { loadPaymentProviders, getPaymentProviderCache, clearPaymentProviderCache }

export const PaymentProviderSelect: React.FC<SelectProps> = (props) => {
  return (
    <EntitySelect
      {...props}
      loadOptions={loadPaymentProviders}
      getCachedOptions={getPaymentProviderCache}
    />
  )
}
