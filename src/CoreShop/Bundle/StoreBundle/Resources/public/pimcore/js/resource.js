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

pimcore.registerNS('coreshop.store.resource');
coreshop.store.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_stores', 'coreshop_store_list');

        pimcore.globalmanager.get('coreshop_stores').load();

        coreshop.broker.fireEvent('resource.register', 'coreshop.store', this);
    },

    openResource: function (item) {
        if (item === 'store') {
            this.openStore();
        }
    },

    openStore: function () {
        try {
            pimcore.globalmanager.get('coreshop_stores_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_stores_panel', new coreshop.store.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.store.resource();
});
