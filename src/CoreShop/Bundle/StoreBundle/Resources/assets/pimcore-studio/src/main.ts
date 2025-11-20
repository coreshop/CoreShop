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
import { StoreBundleIconModule } from './modules/icon-library'
import { StoreManager } from './modules/stores/StoreManager'
import { StoreFormBuilderModule } from './modules/stores/form-builder-module'

const plugin: IAbstractPlugin = {
    name: 'coreshop-store',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(StoreBundleIconModule)
        moduleSystem.registerModule(StoreFormBuilderModule)

        // Register Store Manager widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'coreshop-store-store',
            component: StoreManager
        })
    }
}

export default plugin
