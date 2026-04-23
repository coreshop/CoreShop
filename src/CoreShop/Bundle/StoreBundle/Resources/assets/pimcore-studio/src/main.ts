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
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(StoreBundleIconModule)

        // Register Store Manager widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'coreshop-store-store',
            component: StoreManager
        })
    }
}

export default plugin
