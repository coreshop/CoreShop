/**
 * CoreShop IndexBundle Studio Plugin — index registrations.
 *
 * Registers the index manager widget and the interpreter schema widgets. Registered as a
 * module (not from the plugin's onInit) because the StudioForm widget registry is bound by
 * the StudioFormBundle plugin's own onInit and plugin order is not guaranteed; modules are
 * initialised after every plugin's onInit.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds as pimcoreServiceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry as PimcoreWidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { widgetRegistryServiceId, type WidgetRegistry } from '@coreshop/studio-form'
import { IndexManager } from './IndexManager'
import { InterpreterWidget } from './widgets/InterpreterWidget'
import { InterpreterCollectionWidget } from './widgets/InterpreterCollectionWidget'

export const IndexesModule: AbstractModule = {
    onInit(): void {
        try {
            const widgetManager = container.get<PimcoreWidgetRegistry>(pimcoreServiceIds.widgetManager)

            widgetManager.registerWidget({
                name: 'coreshop-index-index',
                component: IndexManager
            })

            const schemaWidgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)

            // Interpreter widget for dynamic schema loading (no prototypes needed)
            schemaWidgetRegistry.register('coreshop_index_column_interpreter', (field) => ({
                component: InterpreterWidget,
                props: { field },
            }))

            // Interpreter collection widget (nested interpreter lists)
            schemaWidgetRegistry.register('interpreter_collection', (field) => ({
                component: InterpreterCollectionWidget,
                props: { field },
            }))
        } catch {
            // Main Studio app only — widget manager and StudioForm services are absent in
            // the reduced document editor iframe app.
        }
    }
}
