/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface ElementDetail {
  id: number
  type: string
  fullPath: string
  subtype: string | null
  isPublished: boolean
}

interface NicePathTarget {
  id: number
  type: string
}

interface NicePathResponse {
  success: boolean
  data: Record<string, ElementDetail>
}

/**
 * Load detailed information for elements by their IDs
 */
export async function loadElementDetails(ids: string[], type: string = 'object'): Promise<Record<string, ElementDetail>> {
  if (ids.length === 0) {
    return {}
  }

  const targets: NicePathTarget[] = ids.map(id => ({
    id: parseInt(id),
    type
  }))

  const url = '/pimcore-studio/api/coreshop/helper/get-nice-path'
  const formData = new URLSearchParams()
  formData.append('targets', JSON.stringify(targets))
  formData.append('detailed', 'true')

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    credentials: 'same-origin',
    body: formData
  })

  if (!response.ok) {
    throw new Error(`Failed to load element details: ${response.statusText}`)
  }

  const result: NicePathResponse = await response.json()
  return result.data
}
