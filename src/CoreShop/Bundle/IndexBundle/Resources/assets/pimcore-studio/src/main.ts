/**
 * CoreShop IndexBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
// Deep import (bundled, not a shared remote) so the document editable registration also works
// inside the reduced document editor iframe app.
import { registerCoreShopDocumentEditableSelects } from '@coreshop/resource/src/dynamic-types/DynamicTypeDocumentEditableCoreShopSelect'
import { IndexBundleIconModule } from './modules/icon-library'
import { ConditionRegistry, NestedCondition } from './modules/filters/conditions'
import { serviceIds } from './modules/filters/service-ids'
import { FiltersModule } from './modules/filters/module'
import { IndexesModule } from './modules/indexes/module'

const plugin: IAbstractPlugin = {
    name: 'coreshop-index',

    onInit() {
        // Register this bundle's document editables. Runs in the main app and in the reduced
        // document editor iframe; only depends on the core DocumentEditableRegistry.
        registerCoreShopDocumentEditableSelects(['coreshop_filter', 'coreshop_index'])

        // Create and bind separate registries for pre-conditions and user-conditions. These
        // are this bundle's own services, so binding them here makes them available to every
        // other plugin from its onStartup onwards.
        container.bind(serviceIds.preConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(serviceIds.userConditionRegistry).to(ConditionRegistry).inSingletonScope()

        const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
        const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

        // Register NestedCondition manually (recursive, needs ConditionRegistry/ConditionItem)
        // Other conditions are registered dynamically from schema config in FilterManager
        preConditionRegistry.register('nested', NestedCondition)
        userConditionRegistry.register('nested', NestedCondition)
    },

    onStartup({ moduleSystem }) {
        // Widget, object data and schema widget registrations live in these modules: modules
        // are initialised after every plugin's onInit, so the registries other plugins bind
        // in their own onInit are guaranteed to exist by then.
        moduleSystem.registerModule(IndexBundleIconModule)
        moduleSystem.registerModule(FiltersModule)
        moduleSystem.registerModule(IndexesModule)
    }
}

export default plugin
