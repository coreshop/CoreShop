/**
 * CoreShop CurrencyBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { CurrencyBundleIconModule } from './modules/icon-library'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { CurrencyManager } from './modules/currencies/CurrencyManager'
import { ExchangeRateManager } from './modules/exchange-rates/ExchangeRateManager'
import { CurrencyFormBuilderModule } from './modules/currencies/form-builder-module'
import {
    DynamicTypeObjectDataCoreShopCurrency,
    DynamicTypeObjectDataCoreShopCurrencyMultiselect,
    DynamicTypeObjectDataCoreShopMoneyCurrency
} from './dynamic-types'
import { initCurrencyConfig } from './modules/currency-config'

const plugin: IAbstractPlugin = {
    name: 'coreshop-currency',

    onInit() {
        // Load currency config (decimal_factor, decimal_precision) for price formatting
        void initCurrencyConfig()

        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )

        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCurrency())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCurrencyMultiselect())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopMoneyCurrency())
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CurrencyBundleIconModule)
        moduleSystem.registerModule(CurrencyFormBuilderModule)
        // options provider removed; component fetches directly

        // Register Currency entity widget (used by menu)
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        widgets.registerWidget({
            name: 'coreshop-currency-currencies',
            component: CurrencyManager
        })

        widgets.registerWidget({
            name: 'coreshop-currency-exchange-rates',
            component: ExchangeRateManager
        })
    }
}

export default plugin
