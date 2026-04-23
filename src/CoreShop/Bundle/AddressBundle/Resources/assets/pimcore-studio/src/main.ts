/**
 * CoreShop AddressBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import {container, IAbstractPlugin} from '@pimcore/studio-ui-bundle'
import {AddressBundleIconModule} from './modules/icon-library'
import {serviceIds} from '@pimcore/studio-ui-bundle/app'
import type {WidgetRegistry} from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { EntityChoiceWidget } from '@coreshop/resource/src/components/EntityChoiceWidget'
import {ZoneManager} from './modules/zones/ZoneManager'
import { CountryManager } from './modules/countries/CountryManager'
import { StateManager } from './modules/states/StateManager'
import { loadCountries, getCountryCache } from './components/CountrySelect'
import { loadStates, getStateCache } from './components/StateSelect'
import { loadZones, getZoneCache } from './components/ZoneMultiSelect'
import {
    DynamicTypeObjectDataCoreShopCountry,
    DynamicTypeObjectDataCoreShopCountryMultiselect,
    DynamicTypeObjectDataCoreShopState,
    DynamicTypeObjectDataCoreShopAddressIdentifier
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-address-plugin',

    onInit() {
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )

        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCountry())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCountryMultiselect())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopState())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopAddressIdentifier())

        // Register StudioForm widgets for ChoiceTypes
        const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

        formWidgetRegistry.register('coreshop_country_choice', (field) => ({
            component: EntityChoiceWidget,
            props: { loadOptions: loadCountries, getCachedOptions: getCountryCache, droppableAccept: 'coreshop:country', mode: field.multiple ? 'multiple' as const : undefined }
        }))

        formWidgetRegistry.register('coreshop_state_choice', (field) => ({
            component: EntityChoiceWidget,
            props: { loadOptions: loadStates, getCachedOptions: getStateCache, droppableAccept: 'coreshop:state', mode: field.multiple ? 'multiple' as const : undefined }
        }))

        formWidgetRegistry.register('coreshop_zone_choice', (field) => ({
            component: EntityChoiceWidget,
            props: { loadOptions: loadZones, getCachedOptions: getZoneCache, droppableAccept: 'coreshop:zone', mode: field.multiple ? 'multiple' as const : undefined }
        }))
    },

    onStartup({moduleSystem}) {
        moduleSystem.registerModule(AddressBundleIconModule)

        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        widgets.registerWidget({
            name:  'coreshop-address-zones',
            component: ZoneManager
        })
        widgets.registerWidget({
            name: 'coreshop-address-countries',
            component: CountryManager
        })
        widgets.registerWidget({
            name: 'coreshop-address-states',
            component: StateManager
        })
    }
}

export default plugin
