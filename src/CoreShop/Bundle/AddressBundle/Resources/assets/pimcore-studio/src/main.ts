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
import {ZoneManager} from './modules/zones/ZoneManager'
import { CountryManager } from './modules/countries/CountryManager'
import { StateManager } from './modules/states/StateManager'

const plugin: IAbstractPlugin = {
    name: 'coreshop-address-plugin',

    onInit() {},

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
