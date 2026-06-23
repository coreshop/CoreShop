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
import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import { StoreBundleIconModule } from './modules/icon-library';
import { StoreManager } from './modules/stores/StoreManager';
import { StoreFormBuilderModule } from './modules/stores/form-builder-module';
import { DynamicTypeObjectDataCoreShopStore, DynamicTypeObjectDataCoreShopStoreMultiselect } from './dynamic-types';
const plugin = {
    name: 'coreshop-store',
    onInit() {
        const objectDataRegistry = container.get(serviceIds['DynamicTypes/ObjectDataRegistry']);
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopStore());
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopStoreMultiselect());
    },
    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(StoreBundleIconModule);
        moduleSystem.registerModule(StoreFormBuilderModule);
        // Register Store Manager widget
        const widgets = container.get(serviceIds.widgetManager);
        widgets.registerWidget({
            name: 'coreshop-store-store',
            component: StoreManager
        });
    }
};
export default plugin;
