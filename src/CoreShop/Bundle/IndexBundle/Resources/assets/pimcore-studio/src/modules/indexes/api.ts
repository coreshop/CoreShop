/**
 * CoreShop IndexBundle Index API
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource/src/entities/api'
import {FilterApi} from "../filters/api";

/**
 * Index Entity
 */
export interface Index {
  id?: number
  name: string
  class?: string
  worker?: string
  columns?: any[]
  configuration?: Record<string, any>
}

/**
 * Index API - Extends ResourceBundle EntityApi
 */
export class IndexApi extends EntityApi<Index> {
  // Additional index-specific methods can be added here if needed
}

/**
 * Index API instance
 */
export const indexApi = new IndexApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/indices'
})