/**
 * CoreShop CoreBundle Icon Library
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import {type AbstractModule, container} from '@pimcore/studio-ui-bundle'
import {OrderByNumberButton} from "../components/OrderByNumberButton";

import MenuButtonRegistry from '@coreshop/menu-studio-plugin';

export {useOrderByNumber} from '../hooks/useOrderByNumber'
export {orderService} from '../services/OrderService'
export {OrderByNumberButton} from '../components/OrderByNumberButton'

export const CoreBundle: AbstractModule = {
    onInit(): void {
        const buttonRegistry = container.get<MenuButtonRegistry>('CoreShopMenuButtons')

        buttonRegistry.add({
            button: OrderByNumberButton,
            name: 'coreShopOpenOrderByNumberModal',
            icon: 'search',
            label: 'Find Order'
        })
    }
}