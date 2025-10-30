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

import {IAbstractPlugin, container} from '@pimcore/studio-ui-bundle'
import { RuleBundleIconModule } from './modules/icon-library'
import { ActionRegistry, ConditionRegistry, coreshopRuleServiceIds } from './rules/registry'

const plugin: IAbstractPlugin = {
    name: 'coreshop-rule',

    onInit() {
        // Register ActionRegistry and ConditionRegistry as singleton services in the container
        // This allows other bundles to access them via container.get()
        container.bind(coreshopRuleServiceIds.actionRegistry).to(ActionRegistry).inSingletonScope()
        container.bind(coreshopRuleServiceIds.conditionRegistry).to(ConditionRegistry).inSingletonScope()
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(RuleBundleIconModule)
    }
}

export default plugin
