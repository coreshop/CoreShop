/**
 * CoreShop IndexBundle Studio Plugin — filter registrations.
 *
 * Registers the filter manager widget, the coreshop_filter object data type and the schema
 * widgets the filter condition forms need. Registered as a module (not from the plugin's
 * onInit) because the StudioForm widget registry is bound by the StudioFormBundle plugin's
 * own onInit and plugin order is not guaranteed; modules are initialised after every
 * plugin's onInit.
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
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { widgetRegistryServiceId, type WidgetRegistry } from '@coreshop/studio-form'
import { Input } from 'antd'
import { DynamicTypeObjectDataCoreShopFilter } from '../../dynamic-types'
import { FilterFieldSelect, FilterFieldsMultiSelect, FilterValueSelect, FilterValueMultiSelect } from './widgets'
import { FilterManager } from './FilterManager'

export const FiltersModule: AbstractModule = {
    onInit(): void {
        try {
            const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
                pimcoreServiceIds['DynamicTypes/ObjectDataRegistry']
            )
            objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopFilter())

            const widgetManager = container.get<PimcoreWidgetRegistry>(pimcoreServiceIds.widgetManager)

            widgetManager.registerWidget({
                name: 'coreshop-index-filter',
                component: FilterManager
            })

            // Custom schema widgets for filter conditions
            const schemaWidgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)
            schemaWidgetRegistry.register('coreshop_filter_index_field', () => ({ component: FilterFieldSelect }))
            schemaWidgetRegistry.register('coreshop_filter_index_fields', () => ({ component: FilterFieldsMultiSelect }))
            schemaWidgetRegistry.register('coreshop_filter_value_select', () => ({ component: FilterValueSelect }))
            schemaWidgetRegistry.register('coreshop_filter_value_multiselect', () => ({ component: FilterValueMultiSelect }))

            // Hide IndexBundle-owned rule collection prefixes from generic schema forms
            const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
            ;[
                'coreshop_filter_pre_condition_collection',
                'coreshop_filter_user_condition_collection',
            ].forEach((prefix) => schemaWidgetRegistry.register(prefix, hiddenWidget))
        } catch {
            // Everything above targets the main Studio app (object editor, widgets, schema
            // forms). Those services are absent in the reduced document editor iframe app.
        }
    }
}
