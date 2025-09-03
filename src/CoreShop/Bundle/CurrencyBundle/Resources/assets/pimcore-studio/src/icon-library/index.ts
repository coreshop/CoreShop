/**
 * CoreShop CurrencyBundle Icon Library
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

// Import CoreShop Currency icons
import currenciesIcon from '@CoreShopCurrency/assets/currencies.svg?react'
import exchangeRateIcon from '@CoreShopCurrency/assets/exchange_rate.svg?react'

export const CurrencyBundleIconExtension: AbstractModule = {
  name: 'coreshop-currency-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_icon_currency',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_currency',
      component: currenciesIcon
    })

    iconLibrary.register({
      name: 'coreshop_icon_exchange_rate',
      component: exchangeRateIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_exchange_rate',
      component: exchangeRateIcon
    })
  }
}