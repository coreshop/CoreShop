
/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.messenger.resource');

if (coreshop.resource !== undefined) {
    coreshop.messenger.resource = Class.create(coreshop.resource, {
        initialize: function () {
            coreshop.broker.fireEvent('resource.register', 'coreshop.messenger', this);
        },

        openResource: function (item) {
            if (item === 'list') {
                this.openList();
            }
        },

        openList: function () {
            try {
                pimcore.globalmanager.get('coreshop_messenger_list').activate();
            } catch (e) {
                pimcore.globalmanager.add('coreshop_messenger_list', new coreshop.messenger.list());
            }
        },
    });

    coreshop.broker.addListener('pimcore.ready', function () {
        new coreshop.messenger.resource();
    });
}