/**
 * CoreShop ResourceBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { ResourceBundleIconModule } from './modules/icon-library'
import { IAbstractPlugin, container } from "@pimcore/studio-ui-bundle";
import { entityFormExtensionsServiceId, EntityFormExtensionRegistry, entityTableColumnExtensionsServiceId, EntityTableColumnExtensionRegistry } from './entities/extensions'
import { entitySaveDecoratorsServiceId, EntitySaveDecoratorRegistry } from './entities/save-decorators'
import { ResourceConfigProvider, coreshopResourceServiceIds } from './config'

const plugin: IAbstractPlugin = {
    name: 'coreshop-resource',

    onInit() {
        // Bind generic registries used by bundle UIs
        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entityFormExtensionsServiceId)) {
                container.bind(entityFormExtensionsServiceId).to(EntityFormExtensionRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entityFormExtensionsServiceId).to(EntityFormExtensionRegistry).inSingletonScope()
        }

        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entityTableColumnExtensionsServiceId)) {
                container.bind(entityTableColumnExtensionsServiceId).to(EntityTableColumnExtensionRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entityTableColumnExtensionsServiceId).to(EntityTableColumnExtensionRegistry).inSingletonScope()
        }

        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entitySaveDecoratorsServiceId)) {
                container.bind(entitySaveDecoratorsServiceId).to(EntitySaveDecoratorRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entitySaveDecoratorsServiceId).to(EntitySaveDecoratorRegistry).inSingletonScope()
        }

        // Bind resource config provider
        try {
            // @ts-ignore
            if (!(container as any).isBound?.(coreshopResourceServiceIds.configProvider)) {
                container.bind(coreshopResourceServiceIds.configProvider).to(ResourceConfigProvider).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(coreshopResourceServiceIds.configProvider).to(ResourceConfigProvider).inSingletonScope()
        }
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(ResourceBundleIconModule)
    }
}

export default plugin
