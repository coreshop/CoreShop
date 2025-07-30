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

pimcore.registerNS('coreshop.currency.panel');
coreshop.currency.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_currencies_panel',
    storeId: 'coreshop_currencies',
    iconCls: 'coreshop_icon_currency',
    type: 'coreshop_currencies',

    routing: {
        add: 'coreshop_currency_add',
        delete: 'coreshop_currency_delete',
        get: 'coreshop_currency_get',
        list: 'coreshop_currency_list'
    },

    getItemClass: function() {
        return coreshop.currency.item;
    }
});
