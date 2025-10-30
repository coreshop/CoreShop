/**
 * CoreShop StoreBundle Studio Plugin
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

export interface StoreDetail {
  id: number
  name: string
  isDefault?: boolean
  currency?: number
  baseCountry?: number
  countries?: number[]
}

export const storeApi = new EntityApi<StoreDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/stores'
})
