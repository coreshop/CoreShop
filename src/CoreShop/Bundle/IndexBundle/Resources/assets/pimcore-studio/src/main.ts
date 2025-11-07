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
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { IndexBundleIconModule } from './modules/icon-library'
import { ConditionRegistry } from './modules/filters/conditions'
import { serviceIds } from './modules/filters/service-ids'
import {
  RangeCondition,
  SelectCondition,
  MultiselectCondition,
  BooleanCondition,
  SearchCondition,
  CategorySelectCondition,
  CategoryMultiSelectCondition,
  SelectFromMultiselectCondition,
  MultiselectFromMultiselectCondition,
  RelationalSelectCondition,
  RelationalMultiselectCondition,
  NestedCondition
} from './modules/filters/conditions'
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
        // Register widgets
        const widgetManager = container.get<WidgetRegistry>(pimcoreServiceIds.widgetManager)

        widgetManager.registerWidget({
            name: 'coreshop-index-filter',
            component: FilterManager
        })

        widgetManager.registerWidget({
            name: 'coreshop-index-index',
            component: IndexManager
        })

        // Create and bind separate registries for pre-conditions and user-conditions
        container.bind(serviceIds.preConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(serviceIds.userConditionRegistry).to(ConditionRegistry).inSingletonScope()

        // Get registries
        const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
        const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

        // Register filter conditions in BOTH registries
        // (Pre-conditions and User-conditions can use the same condition types)
        const conditionTypes = [
          { type: 'range', component: RangeCondition },
          { type: 'select', component: SelectCondition },
          { type: 'multiselect', component: MultiselectCondition },
          { type: 'boolean', component: BooleanCondition },
          { type: 'search', component: SearchCondition },
          { type: 'category_select', component: CategorySelectCondition },
          { type: 'category_multiselect', component: CategoryMultiSelectCondition },
          { type: 'select_from_multiselect', component: SelectFromMultiselectCondition },
          { type: 'multiselect_from_multiselect', component: MultiselectFromMultiselectCondition },
          { type: 'relational_select', component: RelationalSelectCondition },
          { type: 'relational_multiselect', component: RelationalMultiselectCondition },
          { type: 'nested', component: NestedCondition }
        ]

        conditionTypes.forEach(({ type, component }) => {
          preConditionRegistry.register(type, component)
          userConditionRegistry.register(type, component)
        })

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
        moduleSystem.registerModule(IndexBundleIconModule)
    }
}

export default plugin
