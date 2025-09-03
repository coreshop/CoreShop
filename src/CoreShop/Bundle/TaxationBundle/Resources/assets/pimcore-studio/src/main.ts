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

import { type PluginDefinition } from '@pimcore/studio-ui-bundle'
import { TaxationBundleIconExtension } from './icon-library'

const plugin: PluginDefinition = {
    name: 'coreshop-taxation',
    version: '1.0.0',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(TaxationBundleIconExtension)
    }
}

export default plugin