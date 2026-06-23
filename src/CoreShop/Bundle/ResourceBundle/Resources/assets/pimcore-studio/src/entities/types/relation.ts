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

/**
 * Item in a Many-to-Many relation
 */
export interface ManyToManyRelationValueItem {
  id: number
  type: string
  fullPath: string
  subtype: string | null
  isPublished: boolean
}

/**
 * Value for Many-to-Many relations (array of items)
 */
export type ManyToManyRelationValue = ManyToManyRelationValueItem[]
