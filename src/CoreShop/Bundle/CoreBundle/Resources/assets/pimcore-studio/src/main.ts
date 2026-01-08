/**
 * CoreShop PaymentBundle Studio Plugin
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
import { CoreBundleIconModule } from './modules/icon-library'
import { CoreBundleMenuModule } from './modules/menu'
import { TaxRuleGroupExtensionModule } from './modules/extension/tax-rule-group'
import { CarrierExtensionModule } from './modules/extension/carrier'
import { RuleRegistryExtensionModule } from './modules/extension/rule-registry'
import { SaleTabExtensionModule } from './modules/extension/sale-tab'
import { StoreExtensionModule } from './modules/extension/store'
import { CountryFormExtensionModule } from './modules/extension/country/country-form-extension'
import { OrderCreationExtensionModule } from './modules/extension/order-creation'
import { NotificationRulesExtensionModule } from './modules/extension/notification-rules'
import { ReportsModule } from './modules/reports'

const plugin: IAbstractPlugin = {
    name: 'coreshop-core',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        // ============================================
        // Module Registration
        // ============================================
        // Register extension modules that access other bundles' registries
        // These use lazy initialization to wait for registries to be available
        moduleSystem.registerModule(RuleRegistryExtensionModule)
        moduleSystem.registerModule(SaleTabExtensionModule)
        moduleSystem.registerModule(OrderCreationExtensionModule)
        moduleSystem.registerModule(NotificationRulesExtensionModule)

        // Register other extension modules
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundleMenuModule)
        moduleSystem.registerModule(CountryFormExtensionModule)
        moduleSystem.registerModule(TaxRuleGroupExtensionModule)
        moduleSystem.registerModule(CarrierExtensionModule)
        moduleSystem.registerModule(StoreExtensionModule)
        moduleSystem.registerModule(ReportsModule)
    }
}

export default plugin
