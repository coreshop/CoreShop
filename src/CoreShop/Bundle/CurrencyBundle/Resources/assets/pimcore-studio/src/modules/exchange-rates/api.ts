/**
 * CoreShop CurrencyBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource/src/entities'

export interface ExchangeRate {
  id?: number
  fromCurrency: number
  toCurrency: number
  exchangeRate: number
}

export const exchangeRateApi = new EntityApi<ExchangeRate>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/exchange_rates'
})
