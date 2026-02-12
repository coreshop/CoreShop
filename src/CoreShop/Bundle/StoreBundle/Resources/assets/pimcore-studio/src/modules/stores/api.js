/**
 * CoreShop StoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */
import { EntityApi } from '@coreshop/resource/src/entities';
export const storeApi = new EntityApi({
    basePath: '/pimcore-studio/api',
    resourcePath: '/coreshop/stores'
});
/**
 * Get list of available Pimcore Sites
 */
export const listSites = async () => {
    const response = await fetch('/pimcore-studio/api/coreshop/stores/list-sites', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });
    const data = await response.json();
    return data.data || [];
};
