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

import {AbstractModule} from '@pimcore/studio-ui-bundle'
import {openOrderByNumberModal} from "../../components/OrderByNumberButton";
import {openAboutModal} from "../about/AboutButton";

export {orderService} from '../../services/OrderService'

export const CoreBundleMenuModule: AbstractModule = {
    onInit(): void {
        window.addEventListener('coreshop.order-by-number.open', () => {
            openOrderByNumberModal()
        })

        window.addEventListener('coreshop.about.open', () => {
            openAboutModal()
        })
    }
}
