/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.messenger.resource');

if (coreshop.resource === undefined) {

    document.addEventListener(pimcore.events.pimcoreReady, (e) => {
        if (coreshop.menu.coreshop.messenger) {
            new coreshop.menu.coreshop.messenger();
        }
    });

    document.addEventListener(coreshop.events.menu.open, (e) => {
        var item = e.detail.item;

        if (item.attributes.resource === 'coreshop.messenger' && item.attributes.function === 'list') {
            try {
                pimcore.globalmanager.get('coreshop_messenger_list').activate();
            } catch (e) {
                pimcore.globalmanager.add('coreshop_messenger_list', new coreshop.messenger.list());
            }
        }
    });
}