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

const plugin: IAbstractPlugin = {
    name: 'coreshop-index',

    onInit() {
        // Register Filter widget (Indexes will come later)
        const widgetManager = container.get<WidgetRegistry>(pimcoreServiceIds.widgetManager)

        widgetManager.registerWidget({
            name: 'coreshop-index-filter',
            component: FilterManager
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
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(IndexBundleIconModule)
    }
}

export default plugin
