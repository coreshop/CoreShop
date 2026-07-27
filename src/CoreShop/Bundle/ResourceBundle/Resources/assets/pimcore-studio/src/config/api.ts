/**
 * CoreShop ResourceBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { ResourceConfig } from './types'

export class ResourceConfigApi {
  private readonly basePath: string

  constructor(basePath: string = '/pimcore-studio/api') {
    this.basePath = basePath
  }

  async getConfig(): Promise<ResourceConfig> {
    const url = `${this.basePath}/coreshop/resource/config`
    const res = await fetch(url, { credentials: 'same-origin' })
    if (!res.ok) throw new Error(`Config request failed: ${res.status}`)
    return await res.json() as ResourceConfig
  }
}
