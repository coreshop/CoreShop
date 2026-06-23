/**
 * CoreShop RuleBundle Studio Plugin
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
import { preSeedSchemaCache } from '@coreshop/studio-form'
import type { Rule, RuleConfig } from './types'

export class RuleApi<T extends Rule = Rule> extends EntityApi<T> {
  /**
   * Get rule configuration (available conditions and actions)
   *
   * Also pre-seeds the schema cache with embedded schemas from the response,
   * eliminating the need for separate HTTP requests per condition/action.
   */
  async getConfig(): Promise<RuleConfig> {
    // Access cfg from parent EntityApi class
    const cfg = (this as any).cfg
    const url = `${cfg.basePath}${cfg.resourcePath}/get-config`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`Failed to get config: ${response.statusText}`)
    }

    const config: RuleConfig = await response.json()

    // Pre-seed schema cache with embedded schemas
    if (config.schemas) {
      preSeedSchemaCache(config.schemas)
    }

    return config
  }
}
