/**
 * CoreShop CoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

// @ts-ignore
import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { CoreBundleIconModule } from './modules/icon-library'
import { DynamicTypeObjectDataCoreShopStoreValues } from './dynamic-types'
import { CoreBundleMenuModule } from './modules/menu'
import { RuleRegistryExtensionModule } from './modules/extension/rule-registry'
import { OrderCreationExtensionModule } from './modules/extension/order-creation'
import { ReportsModule } from './modules/reports'
import { SettingsModule } from './modules/settings'
import { AssignToNewCompanyPanel, AssignToExistingCompanyPanel } from './modules/customer-company-assignment'
import { PimcoreRelationWidgetModule } from './modules/pimcore-relation-widget'
import { CustomerAddressSelectWidget } from './modules/extension/order-creation/widgets/CustomerAddressSelectWidget'

const plugin: IAbstractPlugin = {
    name: 'coreshop-core',

    onInit() {
        // Register DynamicTypes
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )

        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopStoreValues())

        // Register custom widgets for order creation schema forms
        const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

        formWidgetRegistry.register('coreshop_customer_address_choice', () => ({
            component: CustomerAddressSelectWidget,
        }))
    },

    onStartup({ moduleSystem }) {
        // Register Customer-to-Company Assignment widgets
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        widgets.registerWidget({
            name: 'coreshop-customer-to-company-assign-to-new',
            component: AssignToNewCompanyPanel,
        })

        widgets.registerWidget({
            name: 'coreshop-customer-to-company-assign-to-existing',
            component: AssignToExistingCompanyPanel,
        })

        // ============================================
        // Module Registration
        // ============================================
        // Register extension modules that access other bundles' registries
        // These use lazy initialization to wait for registries to be available
        moduleSystem.registerModule(RuleRegistryExtensionModule)
        moduleSystem.registerModule(OrderCreationExtensionModule)

        // Register other extension modules
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundleMenuModule)
        moduleSystem.registerModule(PimcoreRelationWidgetModule)
        moduleSystem.registerModule(ReportsModule)
        moduleSystem.registerModule(SettingsModule)
    }
}

export default plugin
