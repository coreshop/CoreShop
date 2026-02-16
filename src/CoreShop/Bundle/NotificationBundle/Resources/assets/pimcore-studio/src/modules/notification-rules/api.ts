/**
 * CoreShop NotificationBundle Studio Plugin
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
import type { NotificationRule, NotificationRuleConfig } from './types'

export class NotificationRuleApi extends EntityApi<NotificationRule> {
  /**
   * Get notification rule configuration (types, conditions, actions)
   */
  async getConfig(): Promise<NotificationRuleConfig> {
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

    const config: NotificationRuleConfig = await response.json()

    // Pre-seed schema cache with embedded schemas
    if (config.schemas) {
      preSeedSchemaCache(config.schemas)
    }

    return config
  }

  /**
   * Sort notification rules
   */
  async sort(ruleId: number, toRuleId: number, position: 'before' | 'after'): Promise<{ success: boolean }> {
    const cfg = (this as any).cfg
    const url = `${cfg.basePath}${cfg.resourcePath}/sort`
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      credentials: 'same-origin',
      body: new URLSearchParams({
        rule: String(ruleId),
        toRule: String(toRuleId),
        position
      })
    })

    if (!response.ok) {
      throw new Error(`Failed to sort: ${response.statusText}`)
    }

    return response.json()
  }
}

export const notificationRuleApi = new NotificationRuleApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/notification_rules'
})
