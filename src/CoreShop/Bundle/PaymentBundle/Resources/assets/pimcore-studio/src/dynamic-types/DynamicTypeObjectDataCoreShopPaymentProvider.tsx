/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadPaymentProviders, getPaymentProviderCache, clearPaymentProviderCache } from '../components/PaymentProviderSelect'

export { loadPaymentProviders, clearPaymentProviderCache }

export class DynamicTypeObjectDataCoreShopPaymentProvider extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopPaymentProvider'
  loadOptions = loadPaymentProviders
  getCachedOptions = getPaymentProviderCache
}
