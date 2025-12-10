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

pimcore.registerNS('coreshop.product.unit.panel');
coreshop.product.unit.panel = Class.create(coreshop.resource.panel, {

    layoutId: 'coreshop_product_unit_panel',
    storeId: 'coreshop_product_units',
    iconCls: 'coreshop_icon_product_units',
    type: 'coreshop_product_units',

    routing : {
        add: 'coreshop_product_unit_add',
        delete: 'coreshop_product_unit_delete',
        get: 'coreshop_product_unit_get',
        list: 'coreshop_product_unit_list'
    },

    getItemClass: function () {
        return coreshop.product.unit.item;
    }
});
