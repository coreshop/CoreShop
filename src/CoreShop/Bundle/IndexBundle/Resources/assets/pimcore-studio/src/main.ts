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
import { serviceIds as pimcoreServiceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry as PimcoreWidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { widgetRegistryServiceId, type WidgetRegistry } from '@coreshop/studio-form'
// Deep import (bundled, not a shared remote) so the document editable registration also works
// inside the reduced document editor iframe app.
import { registerCoreShopDocumentEditableSelects } from '@coreshop/resource/src/dynamic-types/DynamicTypeDocumentEditableCoreShopSelect'
import { Input } from 'antd'
import { IndexBundleIconModule } from './modules/icon-library'
import { DynamicTypeObjectDataCoreShopFilter } from './dynamic-types'
import { ConditionRegistry, NestedCondition } from './modules/filters/conditions'
import { serviceIds } from './modules/filters/service-ids'
import { FilterFieldSelect, FilterFieldsMultiSelect, FilterValueSelect, FilterValueMultiSelect } from './modules/filters/widgets'
import { FilterManager } from './modules/filters/FilterManager'
import { IndexManager } from './modules/indexes/IndexManager'
import { InterpreterWidget } from './modules/indexes/widgets/InterpreterWidget'
import { InterpreterCollectionWidget } from './modules/indexes/widgets/InterpreterCollectionWidget'

const plugin: IAbstractPlugin = {
    name: 'coreshop-index',

    onInit() {
        // Register this bundle's document editables. Runs in the main app and in the reduced
        // document editor iframe; only depends on the core DocumentEditableRegistry.
        registerCoreShopDocumentEditableSelects(['coreshop_filter', 'coreshop_index'])

        try {
        // Register Dynamic Types
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            pimcoreServiceIds['DynamicTypes/ObjectDataRegistry']
        )
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopFilter())

        // Register Pimcore widgets
        const widgetManager = container.get<PimcoreWidgetRegistry>(pimcoreServiceIds.widgetManager)

        widgetManager.registerWidget({
            name: 'coreshop-index-filter',
            component: FilterManager
        })

        widgetManager.registerWidget({
            name: 'coreshop-index-index',
            component: IndexManager
        })

        // Register custom schema widgets for filter conditions
        const schemaWidgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)
        schemaWidgetRegistry.register('coreshop_filter_index_field', () => ({ component: FilterFieldSelect }))
        schemaWidgetRegistry.register('coreshop_filter_index_fields', () => ({ component: FilterFieldsMultiSelect }))
        schemaWidgetRegistry.register('coreshop_filter_value_select', () => ({ component: FilterValueSelect }))
        schemaWidgetRegistry.register('coreshop_filter_value_multiselect', () => ({ component: FilterValueMultiSelect }))

        // Create and bind separate registries for pre-conditions and user-conditions
        container.bind(serviceIds.preConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(serviceIds.userConditionRegistry).to(ConditionRegistry).inSingletonScope()

        // Get registries
        const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
        const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

        // Register NestedCondition manually (recursive, needs ConditionRegistry/ConditionItem)
        // Other conditions are registered dynamically from schema config in FilterManager
        preConditionRegistry.register('nested', NestedCondition)
        userConditionRegistry.register('nested', NestedCondition)

        // Register interpreter widget for dynamic schema loading (no prototypes needed)
        schemaWidgetRegistry.register('coreshop_index_column_interpreter', (field) => ({
            component: InterpreterWidget,
            props: { field },
        }))

        // Register interpreter collection widget (nested interpreter lists)
        schemaWidgetRegistry.register('interpreter_collection', (field) => ({
            component: InterpreterCollectionWidget,
            props: { field },
        }))
        } catch {
            // Everything above targets the main Studio app (object editor, widgets, schema
            // forms). Those services are absent in the reduced document editor iframe app —
            // skip them there; the document editables are already registered above.
        }
    },

    onStartup({ moduleSystem }) {
        try {
            // Hide IndexBundle-owned rule collection prefixes from generic schema forms
            const formWidgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)
            const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
            ;[
              'coreshop_filter_pre_condition_collection',
              'coreshop_filter_user_condition_collection',
            ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))
        } catch {
            // Main Studio app only — StudioForm registry absent in the document editor iframe.
        }

        moduleSystem.registerModule(IndexBundleIconModule)
    }
}

export default plugin
