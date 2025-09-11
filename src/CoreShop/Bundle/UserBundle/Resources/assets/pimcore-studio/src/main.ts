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

import { IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { UserBundleIconModule } from './modules/icon-library'

const plugin: IAbstractPlugin = {
    name: 'coreshop-user',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(UserBundleIconModule)
    }
}

export default plugin
