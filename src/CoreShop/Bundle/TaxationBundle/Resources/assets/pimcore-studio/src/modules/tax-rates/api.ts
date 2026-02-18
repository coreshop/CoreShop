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

import { EntityApi } from '@coreshop/resource/src/entities'

export interface TaxRateTranslation {
  locale: string
  name: string
}

export interface TaxRateDetail extends Record<string, any> {
  id: number
  name: string
  rate: number
  active: boolean
  translations: Record<string, TaxRateTranslation>
}

export const taxRateApi = new EntityApi<TaxRateDetail>({
    basePath: '/pimcore-studio/api',
    resourcePath: '/coreshop/tax_rates'
})
