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
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { EntityChoiceWidget } from '@coreshop/resource/src/components/EntityChoiceWidget'
// Deep import (bundled, not a shared remote) so the document editable registration also works
// inside the reduced document editor iframe app.
import { registerCoreShopDocumentEditableSelects } from '@coreshop/resource/src/dynamic-types/DynamicTypeDocumentEditableCoreShopSelect'
import { TaxRateManager } from './modules/tax-rates/TaxRateManager'
import { TaxRuleGroupManager } from './modules/tax-rule-groups/TaxRuleGroupManager'
import { loadTaxRates, getTaxRateCache } from './components/TaxRateSelect'
import { loadTaxRuleGroups, getTaxRuleGroupCache } from './components/TaxRuleGroupSelect'
import {
    DynamicTypeObjectDataCoreShopTaxRate,
    DynamicTypeObjectDataCoreShopTaxRuleGroup
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-taxation',

    onInit() {
        // Register this bundle's document editables. Runs in the main app and in the reduced
        // document editor iframe; only depends on the core DocumentEditableRegistry.
        registerCoreShopDocumentEditableSelects(['coreshop_tax_rate', 'coreshop_tax_rule_group'])

        try {
            const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
                serviceIds['DynamicTypes/ObjectDataRegistry']
            )

            objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopTaxRate())
            objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopTaxRuleGroup())

            // Register StudioForm widgets for TaxationChoiceTypes
            const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

            formWidgetRegistry.register('coreshop_tax_rule_choice', (field) => ({
                component: EntityChoiceWidget,
                props: { loadOptions: loadTaxRates, getCachedOptions: getTaxRateCache, droppableAccept: 'coreshop:tax_rate', mode: field.multiple ? 'multiple' as const : undefined }
            }))

            formWidgetRegistry.register('coreshop_tax_rule_group_choice', (field) => ({
                component: EntityChoiceWidget,
                props: { loadOptions: loadTaxRuleGroups, getCachedOptions: getTaxRuleGroupCache, droppableAccept: 'coreshop:tax_rule_group', mode: field.multiple ? 'multiple' as const : undefined }
            }))
        } catch {
            // Main Studio app only — object editor / StudioForm services are absent in the
            // reduced document editor iframe. The document editables are already registered above.
        }
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(TaxationBundleIconModule)

        try {
            const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

            widgets.registerWidget({
                name: 'coreshop-taxation-tax-rates',
                component: TaxRateManager
            })
            widgets.registerWidget({
                name: 'coreshop-taxation-tax-rule-groups',
                component: TaxRuleGroupManager
            })
        } catch {
            // Main Studio app only — widget manager absent in the document editor iframe.
        }
    }
}

export default plugin
