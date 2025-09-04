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
import { type PluginDefinition } from '@pimcore/studio-ui-bundle'
import { CoreBundleIconModule } from './icon-library'
import { CoreBundle } from './module'

const plugin: PluginDefinition = {
    name: 'coreshop-core',
    version: '1.0.0',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundle)
    }
}

export default plugin