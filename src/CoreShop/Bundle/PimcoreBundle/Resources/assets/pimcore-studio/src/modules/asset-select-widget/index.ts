/**
 * CoreShop PimcoreBundle - Asset Select Widget Module
 *
 * Registers the 'pimcore_asset_choice' block prefix to render the AssetSelect
 * component in schema-based forms.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { AbstractModule } from '@pimcore/studio-ui-bundle'
import { container } from '@pimcore/studio-ui-bundle'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry } from '@coreshop/studio-form'
import { AssetSelect } from '../../components/AssetSelect'

export const AssetSelectWidgetModule: AbstractModule = {
  onInit(): void {
    const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)

    widgetRegistry.register('pimcore_asset_choice', () => ({
      component: AssetSelect,
    }))
  }
}
