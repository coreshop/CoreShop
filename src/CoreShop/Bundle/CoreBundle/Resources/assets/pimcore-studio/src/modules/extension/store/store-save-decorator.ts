/**
 * CoreShop CoreBundle - Store Save Decorator
 *
 * Extends the Store save payload with CoreBundle-specific fields.
 * CoreBundle has dependencies on other bundles, so it can add
 * fields that reference entities from other bundles (Address, etc.)
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule } from '@pimcore/studio-ui-bundle'
import { getEntitySaveDecoratorRegistry } from '@coreshop/resource/src/entities/save-decorators'
import type { ExtendedStoreDetail } from './store/types'

/**
 * Store Save Decorator Module
 *
 * Adds baseCountry, useGrossPrice, and countries fields to save payload.
 * CoreBundle has dependency on AddressBundle, so it can handle country-related fields.
 */
export const StoreSaveDecoratorModule: AbstractModule = {
  onInit(): void {
    const registry = getEntitySaveDecoratorRegistry()

    if (!registry) {
      return
    }

    registry.add('/coreshop/stores', (payload: any, data: ExtendedStoreDetail) => {
      return {
        ...payload,
        baseCountry: data.baseCountry,
        useGrossPrice: data.useGrossPrice,
        countries: data.countries
      }
    })
  }
}
