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

import { IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { PimcoreBundleIconModule } from './modules/icon-library'

const plugin: IAbstractPlugin = {
    name: 'coreshop-pimcore',

    onInit() {
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
