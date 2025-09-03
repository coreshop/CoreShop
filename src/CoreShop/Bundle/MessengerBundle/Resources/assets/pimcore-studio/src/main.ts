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
import { MessengerBundleIconExtension } from './icon-library'
import { MessengerWidget } from './components'
const plugin: IAbstractPlugin = {
    name: 'coreshop-messenger',
    version: '1.0.0',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(MessengerBundleIconExtension)
        moduleSystem.registerModule(MessengerWidget)
    }
}

export default plugin