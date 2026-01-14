/**
 * CoreShop CoreBundle Menu Module
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import {container, AbstractModule} from '@pimcore/studio-ui-bundle'
import {OrderByNumberButton} from "../../components/OrderByNumberButton";
import {AboutButton} from "../about";

// @ts-ignore
import MenuButtonRegistry from '@coreshop/menu';

export {orderService} from '../../services/OrderService'
export {OrderByNumberButton} from '../../components/OrderByNumberButton'

export const CoreBundleMenuModule: AbstractModule = {
    onInit(): void {
        const buttonRegistry = container.get<MenuButtonRegistry>('CoreShopMenuButtons')

        buttonRegistry.add({
            button: OrderByNumberButton,
            name: 'coreShopOpenOrderByNumberModal',
        })

        buttonRegistry.add({
            button: AboutButton,
            name: 'coreShopAbout',
        })
    }
}