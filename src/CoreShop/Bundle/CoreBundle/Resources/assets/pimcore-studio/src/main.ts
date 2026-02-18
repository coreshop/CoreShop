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
import { registerMenuButton } from '@coreshop/menu/src'
import { CoreBundleIconModule } from './modules/icon-library'
import { DynamicTypeObjectDataCoreShopStoreValues } from './dynamic-types'
import { CoreBundleMenuModule } from './modules/menu'
import { RuleRegistryExtensionModule } from './modules/extension/rule-registry'
import { OrderCreationExtensionModule } from './modules/extension/order-creation'
import { ReportsModule } from './modules/reports'
import { SettingsModule } from './modules/settings'
import { PimcoreRelationWidgetModule } from './modules/pimcore-relation-widget'
import { CustomerAddressSelectWidget } from './modules/extension/order-creation/widgets/CustomerAddressSelectWidget'
import { AssignToNewCompanyButton } from './components/AssignToNewCompanyButton'
import { AssignToExistingCompanyButton } from './components/AssignToExistingCompanyButton'
import {
  AssignToNewCompanyPanel,
  AssignToExistingCompanyPanel,
  customerCompanyAssignmentWidgetRestorer,
} from './modules/customer-company-assignment'

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

        registerMenuButton({
          name: 'coreshopAssignCustomerToNewCompany',
          button: AssignToNewCompanyButton,
        })

        registerMenuButton({
          name: 'coreshopAssignCustomerToExistingCompany',
          button: AssignToExistingCompanyButton,
        })
    },

    onStartup({ moduleSystem }) {
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        // Persistent detail widgets (one per customer/company selection)
        widgets.registerWidget({
            name: 'coreshop-customer-to-company-assign-to-new-detail',
            component: AssignToNewCompanyPanel,
        })
        widgets.registerWidget({
            name: 'coreshop-customer-to-company-assign-to-existing-detail',
            component: AssignToExistingCompanyPanel,
        })

        // Register widget restorer for browser reload persistence
        const widgetRestorerRegistry = container.get<any>((serviceIds as any).widgetRestorerRegistry)
        if (widgetRestorerRegistry) {
          widgetRestorerRegistry.register(customerCompanyAssignmentWidgetRestorer)
        }

        // ============================================
        // Module Registration
        // ============================================
        moduleSystem.registerModule(RuleRegistryExtensionModule)
        moduleSystem.registerModule(OrderCreationExtensionModule)
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundleMenuModule)
        moduleSystem.registerModule(PimcoreRelationWidgetModule)
        moduleSystem.registerModule(ReportsModule)
        moduleSystem.registerModule(SettingsModule)
    }
}

export default plugin
