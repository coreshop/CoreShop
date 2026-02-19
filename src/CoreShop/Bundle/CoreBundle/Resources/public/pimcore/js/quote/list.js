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

pimcore.registerNS('coreshop.order.quote.list');
coreshop.order.quote.list = Class.create(coreshop.order.quote.list, {
    storeRenderer: function (val) {
        var stores = pimcore.globalmanager.get('coreshop_stores');
        var store = stores.getById('id', String(val));
        if (store) {
            return store.get('name');
        }

        return null;
    }
});

