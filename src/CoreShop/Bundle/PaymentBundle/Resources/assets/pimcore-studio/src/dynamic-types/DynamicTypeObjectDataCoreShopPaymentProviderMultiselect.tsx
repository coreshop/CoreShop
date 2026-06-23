/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeObjectDataCoreShopMultiSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopMultiSelect'
import { loadPaymentProviders, getPaymentProviderCache } from '../components/PaymentProviderSelect'

export class DynamicTypeObjectDataCoreShopPaymentProviderMultiselect extends DynamicTypeObjectDataCoreShopMultiSelect {
  readonly id = 'coreShopPaymentProviderMultiselect'
  loadOptions = loadPaymentProviders
  getCachedOptions = getPaymentProviderCache
}
