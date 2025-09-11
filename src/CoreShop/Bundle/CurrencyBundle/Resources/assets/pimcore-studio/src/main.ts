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
import { CurrencyManager } from './modules/currencies/CurrencyManager'

const plugin: IAbstractPlugin = {
    name: 'coreshop-currency',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CurrencyBundleIconModule)
        // options provider removed; component fetches directly

        // Register Currency entity widget (used by menu)
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        const widgetId = 'coreshop-currency-currencies'
        widgets.registerWidget({
            name: widgetId,
            component: CurrencyManager
        })
    }
}

export default plugin
