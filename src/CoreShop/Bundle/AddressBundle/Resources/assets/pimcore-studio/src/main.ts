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
import {ZoneManager} from './modules/zones/ZoneManager'
import { CountryManager } from './modules/countries/CountryManager'
import { StateManager } from './modules/states/StateManager'
import { CountryFormBuilderModule } from './modules/countries/form-builder-module'
import { ZoneFormBuilderModule } from './modules/zones/form-builder-module'
import { StateFormBuilderModule } from './modules/states/form-builder-module'
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
    },

    onStartup({moduleSystem}) {
        moduleSystem.registerModule(AddressBundleIconModule)
        moduleSystem.registerModule(CountryFormBuilderModule)
        moduleSystem.registerModule(ZoneFormBuilderModule)
        moduleSystem.registerModule(StateFormBuilderModule)

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
