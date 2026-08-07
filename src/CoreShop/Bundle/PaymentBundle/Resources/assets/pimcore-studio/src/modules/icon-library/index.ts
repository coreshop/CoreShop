/**
 * CoreShop PaymentBundle Icon Library
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'

// @ts-ignore
import paymentProviderIcon from '../../assets/payment-provider.svg?react'
// @ts-ignore
import paymentProviderRuleIcon from '../../assets/payment-provider-rule.svg?react'

export const PaymentBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'pimcore_icon_coreShopPaymentProvider',
      component: paymentProviderIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_payment_provider',
      component: paymentProviderIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_payment_provider',
      component: paymentProviderIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_payment_provider_rule',
      component: paymentProviderRuleIcon
    })
  }
}