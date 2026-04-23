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

export interface StoreDetail extends Record<string, any> {
  id?: number
  name: string
  siteId?: number | null
  template?: string
  currency?: number | null
  active?: boolean
}

export interface Site {
  id: number
  name: string
}

export const storeApi = new EntityApi<StoreDetail>({
    basePath: '/pimcore-studio/api',
    resourcePath: '/coreshop/stores'
})

/**
 * Get list of available Pimcore Sites
 */
export const listSites = async (): Promise<Site[]> => {
  const response = await fetch('/pimcore-studio/api/coreshop/stores/list-sites', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  })

  const data = await response.json()
  return data.data || []
}
