/**
 * CoreShop StoreBundle Studio Plugin
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
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { EntityChoiceWidget } from '@coreshop/resource/src/components/EntityChoiceWidget'
// Deep import (bundled, not a shared remote) so the document editable registration also works
// inside the reduced document editor iframe app.
import { registerCoreShopDocumentEditableSelects } from '@coreshop/resource/src/dynamic-types/DynamicTypeDocumentEditableCoreShopSelect'
import { StoreBundleIconModule } from './modules/icon-library'
import { StoreManager } from './modules/stores/StoreManager'
import { loadStores, getStoreCache } from './components/StoreSelect'
import {
    DynamicTypeObjectDataCoreShopStore,
    DynamicTypeObjectDataCoreShopStoreMultiselect
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-store',

    onInit() {
        // Register this bundle's document editables. Runs in the main app and in the reduced
        // document editor iframe; only depends on the core DocumentEditableRegistry.
        registerCoreShopDocumentEditableSelects(['coreshop_store'])

        try {
            const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
                serviceIds['DynamicTypes/ObjectDataRegistry']
            )

            objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopStore())
            objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopStoreMultiselect())

            // Register StudioForm widget for StoreChoiceType
            const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

            formWidgetRegistry.register('coreshop_store_choice', (field) => ({
                component: EntityChoiceWidget,
                props: { loadOptions: loadStores, getCachedOptions: getStoreCache, droppableAccept: 'coreshop:store', mode: field.multiple ? 'multiple' as const : undefined }
            }))
        } catch {
            // Main Studio app only — object editor / StudioForm services are absent in the
            // reduced document editor iframe. The document editables are already registered above.
        }
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(StoreBundleIconModule)

        try {
            // Register Store Manager widget
            const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
            widgets.registerWidget({
                name: 'coreshop-store-store',
                component: StoreManager
            })
        } catch {
            // Main Studio app only — widget manager absent in the document editor iframe.
        }
    }
}

export default plugin
