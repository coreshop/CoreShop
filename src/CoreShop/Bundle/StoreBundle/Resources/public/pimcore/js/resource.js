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
