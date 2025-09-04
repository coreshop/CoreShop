/**
 * CoreShop MessengerBundle Studio Plugin
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
import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { MessengerBundleIconModule } from './icon-library'
import { MessengerModule } from './module'
const plugin: IAbstractPlugin = {
    name: 'coreshop-messenger',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(MessengerBundleIconModule)
        moduleSystem.registerModule(MessengerModule)
    }
}

export default plugin