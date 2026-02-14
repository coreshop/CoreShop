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
import { Input } from 'antd'
import { IndexBundleIconModule } from './modules/icon-library'
import { DynamicTypeObjectDataCoreShopFilter } from './dynamic-types'
import { ConditionRegistry, NestedCondition } from './modules/filters/conditions'
import { serviceIds } from './modules/filters/service-ids'
import { FilterFieldSelect, FilterFieldsMultiSelect, FilterValueSelect, FilterValueMultiSelect } from './modules/filters/widgets'
import { FilterManager } from './modules/filters/FilterManager'
import { IndexManager } from './modules/indexes/IndexManager'
import { GetterConfiguratorRegistry, InterpreterConfiguratorRegistry, WorkerConfiguratorRegistry } from './modules/indexes/registry'
import { serviceIds as indexServiceIds } from './modules/indexes/service-ids'
import {
  MysqlWorkerConfigurator,
  OpenSearchWorkerConfigurator,
  BrickGetterConfigurator,
  FieldcollectionGetterConfigurator,
  ClassificationStoreGetterConfigurator,
  ObjectPropertyGetterConfigurator,
  ExpressionInterpreterConfigurator,
  ObjectPropertyInterpreterConfigurator,
  NestedInterpreterConfigurator,
  IteratorInterpreterConfigurator
} from './modules/indexes/configurators'

const plugin: IAbstractPlugin = {
    name: 'coreshop-index',

    onInit() {
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

        // Create and bind getter/interpreter/worker configurator registries for indices
        container.bind(indexServiceIds.getterConfiguratorRegistry).to(GetterConfiguratorRegistry).inSingletonScope()
        container.bind(indexServiceIds.interpreterConfiguratorRegistry).to(InterpreterConfiguratorRegistry).inSingletonScope()
        container.bind(indexServiceIds.workerConfiguratorRegistry).to(WorkerConfiguratorRegistry).inSingletonScope()

        // Get registries
        const getterConfiguratorRegistry = container.get<GetterConfiguratorRegistry>(indexServiceIds.getterConfiguratorRegistry)
        const interpreterConfiguratorRegistry = container.get<InterpreterConfiguratorRegistry>(indexServiceIds.interpreterConfiguratorRegistry)
        const workerConfiguratorRegistry = container.get<WorkerConfiguratorRegistry>(indexServiceIds.workerConfiguratorRegistry)

        // Register worker configurators
        workerConfiguratorRegistry.register('mysql', MysqlWorkerConfigurator)
        workerConfiguratorRegistry.register('opensearch', OpenSearchWorkerConfigurator)

        // Register getter configurators (types must match backend service tags)
        getterConfiguratorRegistry.register('brick', BrickGetterConfigurator)
        getterConfiguratorRegistry.register('fieldcollection', FieldcollectionGetterConfigurator)
        getterConfiguratorRegistry.register('classificationstore', ClassificationStoreGetterConfigurator)
        getterConfiguratorRegistry.register('objectproperty', ObjectPropertyGetterConfigurator)

        // Register interpreter configurators (types must match backend service tags)
        interpreterConfiguratorRegistry.register('expression', ExpressionInterpreterConfigurator)
        interpreterConfiguratorRegistry.register('objectProperty', ObjectPropertyInterpreterConfigurator)
        interpreterConfiguratorRegistry.register('nested', NestedInterpreterConfigurator)
        interpreterConfiguratorRegistry.register('nestedLocalized', NestedInterpreterConfigurator)
        interpreterConfiguratorRegistry.register('nestedRelational', NestedInterpreterConfigurator)
        interpreterConfiguratorRegistry.register('iterator', IteratorInterpreterConfigurator)

        // Note: Default getter/interpreter configurators will be used automatically if no specific one is registered
        // Specific configurators can be registered here or via extensions
    },

    onStartup({ moduleSystem }) {
        // Hide IndexBundle-owned rule collection prefixes from generic schema forms
        const formWidgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)
        const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
        ;[
          'coreshop_filter_pre_condition_collection',
          'coreshop_filter_user_condition_collection',
        ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))

        moduleSystem.registerModule(IndexBundleIconModule)
    }
}

export default plugin
