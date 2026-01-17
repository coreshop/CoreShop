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
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { TaxRateManager } from './modules/tax-rates/TaxRateManager'
import { TaxRuleGroupManager } from './modules/tax-rule-groups/TaxRuleGroupManager'
import { TaxRateFormBuilderModule } from './modules/tax-rates/form-builder-module'
import { TaxRuleGroupFormBuilderModule } from './modules/tax-rule-groups/form-builder-module'
import {
    DynamicTypeObjectDataCoreShopTaxRate,
    DynamicTypeObjectDataCoreShopTaxRuleGroup
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-taxation',

    onInit() {
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )

        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopTaxRate())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopTaxRuleGroup())
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(TaxationBundleIconModule)
        moduleSystem.registerModule(TaxRateFormBuilderModule)
        moduleSystem.registerModule(TaxRuleGroupFormBuilderModule)

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
