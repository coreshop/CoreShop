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

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { PimcoreBundleIconModule } from './modules/icon-library'
import {
    DynamicTypeObjectDataCoreShopSerializedData,
    DynamicTypeObjectDataCoreShopDynamicDropdown,
    DynamicTypeObjectDataCoreShopDynamicDropdownMultiple,
    DynamicTypeObjectDataCoreShopItemSelector,
    DynamicTypeObjectDataCoreShopSuperBoxSelect
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-pimcore',

    // Base plugin of the CoreShop Studio stack: it registers the dynamic types the other
    // CoreShop plugins build on. Plugins are initialised in ascending priority order
    // (default 0), so this runs before coreshop-resource, coreshop-studio-form-plugin and
    // every feature bundle.
    priority: -1200,

    onInit() {
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )

        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopSerializedData())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopDynamicDropdown())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopDynamicDropdownMultiple())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopItemSelector())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopSuperBoxSelect())
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(PimcoreBundleIconModule)
    }
}

export default plugin

// Note: React components, hooks, and contexts are NOT exported from main.ts
// to avoid Module Federation issues with React instance sharing.
//
// Import from sub-paths instead:
//   import { GridFilterDropdown } from '@coreshop/pimcore/src/modules/grid/components/GridFilterDropdown'
//   import { PresetFilterProvider, usePresetFilter } from '@coreshop/pimcore/src/modules/grid/context/PresetFilterContext'
//   import { createPresetFilterDecorator } from '@coreshop/pimcore/src/modules/grid/decorators'
//   import { fetchGridFilters } from '@coreshop/pimcore/src/modules/grid/api'
//   import { GRID_EVENTS } from '@coreshop/pimcore/src/modules/grid/events'
//   import { gridServiceIds } from '@coreshop/pimcore/src/modules/grid/service-ids'
//   import { coreshopBroker } from '@coreshop/pimcore/src/modules/broker'
