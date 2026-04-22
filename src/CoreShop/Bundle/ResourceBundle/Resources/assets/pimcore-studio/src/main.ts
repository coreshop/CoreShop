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
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import {
    DynamicTypeObjectDataCoreShopRelation,
    DynamicTypeObjectDataCoreShopRelations
} from './dynamic-types'
import {
  entityFormExtensionsServiceId,
  EntityFormExtensionRegistry,
  entityTableColumnExtensionsServiceId,
  EntityTableColumnExtensionRegistry,
  entityTabExtensionsServiceId,
  EntityTabExtensionRegistry,
  entityActionExtensionsServiceId,
  EntityActionExtensionRegistry,
  entityValidationExtensionsServiceId,
  EntityValidationExtensionRegistry,
  entityLifecycleHooksServiceId,
  EntityLifecycleHookRegistry
} from './entities/extensions'
import { entitySaveDecoratorsServiceId, EntitySaveDecoratorRegistry } from './entities/save-decorators'
import { ResourceConfigProvider, coreshopResourceServiceIds } from './config'

const plugin: IAbstractPlugin = {
    name: 'coreshop-resource',

    onInit() {
        // Register CoreShop Dynamic Types for Data Objects
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopRelation())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopRelations())

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

        // Tab Extensions
        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entityTabExtensionsServiceId)) {
                container.bind(entityTabExtensionsServiceId).to(EntityTabExtensionRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entityTabExtensionsServiceId).to(EntityTabExtensionRegistry).inSingletonScope()
        }

        // Action Extensions
        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entityActionExtensionsServiceId)) {
                container.bind(entityActionExtensionsServiceId).to(EntityActionExtensionRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entityActionExtensionsServiceId).to(EntityActionExtensionRegistry).inSingletonScope()
        }

        // Validation Extensions
        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entityValidationExtensionsServiceId)) {
                container.bind(entityValidationExtensionsServiceId).to(EntityValidationExtensionRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entityValidationExtensionsServiceId).to(EntityValidationExtensionRegistry).inSingletonScope()
        }

        // Lifecycle Hooks
        try {
            // @ts-ignore
            if (!(container as any).isBound?.(entityLifecycleHooksServiceId)) {
                container.bind(entityLifecycleHooksServiceId).to(EntityLifecycleHookRegistry).inSingletonScope()
            }
        } catch (e) {
            // @ts-ignore
            container.bind(entityLifecycleHooksServiceId).to(EntityLifecycleHookRegistry).inSingletonScope()
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
