/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.store.resource');
coreshop.store.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_stores', 'coreshop/stores');

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