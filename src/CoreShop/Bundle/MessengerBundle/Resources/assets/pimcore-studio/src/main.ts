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
import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { MessengerBundleIconModule } from './modules/icon-library'
import { MessengerModule } from './module'
import { MessengerMessageHandler } from './modules/mercure/messenger-message-handler'

const plugin: IAbstractPlugin = {
    name: 'coreshop-messenger',

    onInit() {
        // Register our message handler with Pimcore's GlobalMessageBus
        // This must happen in onInit, before Pimcore starts the global subscription
        try {
            const globalMessageBus = container.get<any>(serviceIds.globalMessageBus)
            const handler = new MessengerMessageHandler()
            globalMessageBus.registerHandler(handler)
        } catch (error) {
            console.warn('CoreShop MessengerBundle: Failed to register Mercure message handler', error)
        }
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(MessengerBundleIconModule)
        moduleSystem.registerModule(MessengerModule)
    }
}

export default plugin
