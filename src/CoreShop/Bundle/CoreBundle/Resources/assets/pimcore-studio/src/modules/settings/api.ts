/**
 * CoreShop CoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

/**
 * Configuration data keyed by store ID, each containing key-value pairs
 */
export type ConfigurationData = Record<string, Record<string, any>>

interface GetAllResponse {
  success: boolean
  data: ConfigurationData
}

interface SaveAllResponse {
  success: boolean
  message?: string
}

/**
 * Settings API for per-store configuration
 */
export const settingsApi = {
  async getAll(): Promise<ConfigurationData> {
    const response = await fetch('/admin/coreshop/configurations/get-all')

    if (!response.ok) {
      throw new Error(`Failed to fetch configuration: ${response.statusText}`)
    }

    const result: GetAllResponse = await response.json()

    if (!result.success) {
      throw new Error('Failed to fetch configuration')
    }

    return result.data
  },

  async saveAll(values: ConfigurationData): Promise<void> {
    const formData = new FormData()
    formData.append('values', JSON.stringify(values))

    const response = await fetch('/admin/coreshop/configurations/save-all', {
      method: 'POST',
      body: formData
    })

    if (!response.ok) {
      throw new Error(`Failed to save configuration: ${response.statusText}`)
    }

    const result: SaveAllResponse = await response.json()

    if (!result.success) {
      throw new Error(result.message ?? 'Failed to save configuration')
    }
  }
}
