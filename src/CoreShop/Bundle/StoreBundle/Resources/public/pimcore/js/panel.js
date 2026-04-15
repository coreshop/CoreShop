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

pimcore.registerNS('coreshop.store.panel');
coreshop.store.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_stores_panel',
    storeId: 'coreshop_stores',
    iconCls: 'coreshop_icon_store',
    type: 'coreshop_stores',

    routing: {
        add: 'coreshop_store_add',
        delete: 'coreshop_store_delete',
        get: 'coreshop_store_get',
        list: 'coreshop_store_list'
    },

    getItemClass: function() {
        return coreshop.store.item;
    }
});
