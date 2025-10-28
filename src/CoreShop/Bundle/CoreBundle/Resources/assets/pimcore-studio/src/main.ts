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
import {IAbstractPlugin} from '@pimcore/studio-ui-bundle'
import { CoreBundleIconModule } from './modules/icon-library'
import { CoreBundleMenuModule } from './modules/menu'
import { CountryExtensionModule } from './modules/extension/country'
import { TaxRuleGroupExtensionModule } from './modules/extension/tax-rule-group'

const plugin: IAbstractPlugin = {
    name: 'coreshop-core',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundleMenuModule)
        moduleSystem.registerModule(CountryExtensionModule)
        moduleSystem.registerModule(TaxRuleGroupExtensionModule)
    }
}

export default plugin
