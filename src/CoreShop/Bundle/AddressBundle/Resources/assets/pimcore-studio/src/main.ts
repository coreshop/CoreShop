/**
 * CoreShop AddressBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type PluginDefinition } from '@pimcore/studio-ui-bundle'
import { AddressBundleIconModule } from './icon-library'

const plugin: PluginDefinition = {
    name: 'coreshop-address-plugin',
    version: '1.0.0',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(AddressBundleIconModule)
    }
}

export default plugin