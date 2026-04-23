/**
 * CoreShop StoreBundle Icon Library
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */
import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
// @ts-ignore
import storeIcon from '../../assets/store.svg?react';
export const StoreBundleIconModule = {
    onInit() {
        const iconLibrary = container.get(serviceIds.iconLibrary);
        iconLibrary.register({
            name: 'coreshop_store',
            component: storeIcon
        });
        iconLibrary.register({
            name: 'coreshop_nav_icon_store',
            component: storeIcon
        });
        iconLibrary.register({
            name: 'coreshop_stores',
            component: storeIcon
        });
    }
};
