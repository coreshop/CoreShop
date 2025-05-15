/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
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