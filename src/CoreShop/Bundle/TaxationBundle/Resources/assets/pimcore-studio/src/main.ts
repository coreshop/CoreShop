/**
 * CoreShop TaxationBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { container, IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { TaxationBundleIconModule } from './modules/icon-library'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { TaxRateManager } from './modules/tax-rates/TaxRateManager'
import { TaxRuleGroupManager } from './modules/tax-rule-groups/TaxRuleGroupManager'

const plugin: IAbstractPlugin = {
    name: 'coreshop-taxation',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(TaxationBundleIconModule)

        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        widgets.registerWidget({
            name: 'coreshop-taxation-tax-rates',
            component: TaxRateManager
        })
        widgets.registerWidget({
            name: 'coreshop-taxation-tax-rule-groups',
            component: TaxRuleGroupManager
        })
    }
}

export default plugin
